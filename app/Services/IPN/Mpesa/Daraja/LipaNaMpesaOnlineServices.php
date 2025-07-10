<?php

namespace App\Services\IPN\Mpesa\Daraja;

use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LipaNaMpesaOnlineServices
{
    private $timestamp;

    public function __construct()
    {
        $this->timestamp = Carbon::parse(REQUEST_TIMESTAMP)->format('YmdHis');
    }

    private function fetchAccessToken(): string
    {
        return Cache::remember('ACCESS_TOKEN', now()->addMinutes(59), function () {
            return $this->generateAccessToken();
        });
    }

    private function generateAccessToken(): string
    {
        $url = 'https://' . getSetting('MPESA_LNMO_ENVIRONMENT') . '.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $response = Http::acceptJson()
            ->withToken(base64_encode(getSetting('MPESA_LNMO_CONSUMER_KEY') . ':' . getSetting('MPESA_LNMO_CONSUMER_SECRET')), 'Basic')
            ->get($url);

        return optional($response->object())->access_token;
    }

    private function submit(string $url, array $data): object|null
    {
        $accessToken = $this->fetchAccessToken();

        if ($accessToken) {
            $response = Http::acceptJson()
                ->withToken($accessToken)
                ->post($url, $data);

            return $response->object();
        }
    }
    
    public function transact(array $data): array|null
    {
        // Validate the request parameters
        $validator = Validator::make($data, [
            'amount' => 'required|numeric|min:0.01',
            'telephone' => [
                'required',
                'telephone',
            ],
            'reference' => 'required|string|exists:invoices,_uid',
            'description' => 'required|string',
            'category' => 'required|string',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            // Throw a ValidationException with the first validation error
            throw new ValidationException($validator);
        }

        $url = 'https://' . getSetting('MPESA_LNMO_ENVIRONMENT') . '.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $request = [
            'BusinessShortCode' => getSetting('MPESA_LNMO_SHORT_CODE'),
            'Password' => base64_encode(getSetting('MPESA_LNMO_SHORT_CODE') . getSetting('MPESA_LNMO_PASS_KEY') . $this->timestamp),
            'Timestamp' => $this->timestamp,
            'TransactionType' => Transaction::CUSTOMER_PAY_BILL_ONLINE,
            'Amount' => $data['amount'],
            'PartyA' => str_replace('+', '', phoneNumberPrefix($data['telephone'])), // depositor
            'PartyB' => getSetting('MPESA_LNMO_SHORT_CODE'),
            'PhoneNumber' => str_replace('+', '', phoneNumberPrefix($data['telephone'])),
            'CallBackURL' => secure_url(URL::route('mpesa.daraja.lnmo.callback', [], false)),
            'AccountReference' => $data['reference'],
            'TransactionDesc' => 'invoice #' . $data['reference'] . ' settlement processing', // $data['description']
        ];

        $response = $this->submit($url, $request);

        if (isset($response->ResponseCode) && $response->ResponseCode == 0) {
            $transaction = Transaction::create([
                '_uid' => generateUID(Transaction::class, 10),
                'party_a' => $request['PhoneNumber'],
                'party_b' => getSetting('MPESA_LNMO_SHORT_CODE'),
                'account_reference' => $request['AccountReference'],
                'transaction_category' => $data['category'],
                'transaction_type' => Transaction::getMpesaDarajaApiTransactionTypeValueByLabel($request['TransactionType']),
                'transaction_channel' => Transaction::C2B,
                'transaction_aggregator' => Transaction::MPESA_KE,
                'transaction_id' => $response->CheckoutRequestID,
                'transaction_amount' => $request['Amount'],
                'transaction_timestamp' => $this->timestamp,
                'transaction_details' => $request['TransactionDesc'], // $data['description']
                '_response' => collect([$this->timestamp => ['transact' => (array) $response]])->toJson(),
                '_status' => Transaction::PROCESSING
            ]);

            return $transaction->toArray();
        }
    }

    public function query(string $transaction_id): array|null
    {
        $transaction = Transaction::where('transaction_id', $transaction_id)->first();

        if ($transaction) {
            $url = 'https://' . getSetting('MPESA_LNMO_ENVIRONMENT') . '.safaricom.co.ke/mpesa/stkpushquery/v1/query';

            $data = [
                'BusinessShortCode' => getSetting('MPESA_LNMO_SHORT_CODE'),
                'Password' => base64_encode(getSetting('MPESA_LNMO_SHORT_CODE') . getSetting('MPESA_LNMO_PASS_KEY') . $this->timestamp),
                'Timestamp' => $this->timestamp,
                'CheckoutRequestID' => $transaction_id
            ];

            $response = $this->submit($url, $data);

            $transaction->_response = collect(array_merge(json_decode($transaction->_response, true), ['query' => $data]))->toJson();
            $transaction->_status = $response->ResultCode == 0 ? Transaction::ACCEPTED : Transaction::REJECTED;
            $transaction->save();

            return $transaction->toArray();
        }
    }

    public function callback(array $request): array|null
    {
        $callback = Arr::get($request, 'Body.stkCallback', []);
        $transaction = Transaction::where('transaction_id', Arr::get($callback, 'CheckoutRequestID'))->first();

        if ($transaction) {
            $data = [
                '_response' => collect(array_merge(json_decode($transaction->_response, true), [$this->timestamp => ['callback' => (array) $request]]))->toJson(),
            ];

            if (isset($callback['ResultCode']) && $callback['ResultCode'] === 0 && Arr::get($callback, 'CallbackMetadata.Item.1.Value')) {
                $data['transaction_code'] = Arr::get($callback, 'CallbackMetadata.Item.1.Value');
                $data['_status'] = Transaction::ACCEPTED;
            } else {
                $data['_status'] = Transaction::REJECTED;
            }

            $transaction->update($data);

            return $transaction->toArray();
        }
    }
}
