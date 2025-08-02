<?php

namespace App\Services\IAN;

use App\Models\Profile;
use Illuminate\Support\Carbon;
use App\Models\OutboundTextMessage;
use Illuminate\Support\Facades\Http;

class AfricaIsTalkingServices
{
    public function send(string $recipient, string $content): array|null
    {
        // Initialize Africa's Talking service
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => getSetting('AFRICAS_TALKING_API_KEY'),
        ])->asForm()->post(getSetting('AFRICAS_TALKING_MESSAGING_ENDPOINT'), array_filter([
            'username' => getSetting('AFRICAS_TALKING_USERNAME'),
            'to' => $recipient,
            'message' => $content,
            'from' => getSetting('AFRICAS_TALKING_SENDER_ID') ?? NULL,
        ]));

        // Validate if the response was successful
        if ($response->successful()) {
            $data = collect($response->json()['SMSMessageData']);
            if (isset($data['Recipients']) && !empty($data['Recipients'])) {
                return array_filter([
                    'transaction_amount' => (getOnlyNumbers(trim($data['Recipients'][0]['cost'])) + 0.2),
                    'transaction_id' => optional($data['Recipients'][0])['messageId'] != 'None' ? trim($data['Recipients'][0]['messageId']) : NULL,
                    '_status' => OutboundTextMessage::getStatusValueByLabel(OutboundTextMessage::getStatusOptions()[(int) trim($data['Recipients'][0]['statusCode'])]),
                ]);
            }
        }
    }

    public function accountBalance(): array|null
    {
        // Initialize Africa's Talking service
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => getSetting('AFRICAS_TALKING_API_KEY'),
        ])->get(getSetting('AFRICAS_TALKING_USER_ENDPOINT'), [
            'username' => getSetting('AFRICAS_TALKING_USERNAME'),
        ]);

        if ($response->successful()) {
            $data = collect($response->json())->flatten();

            return [
                'account_service' => get_class($this),
                'last_checked_at' => Carbon::parse(REQUEST_TIMESTAMP)->toDateTimeString(),
                'account_balance' => optional($data)[0] ? getOnlyNumbers(trim($data[0])) : 0,
            ];
        }
    }

    public function deliveryReports(array $request): void
    {
        // Retrieve the text message using the transaction ID from the request
        $textMessage = OutboundTextMessage::where('transaction_id', trim($request['id']))->firstOrFail();

        // Determine the status to update based on the presence of a failure reason
        $status = isset($request['failureReason'])
            ? OutboundTextMessage::getStatusValueByLabel(trim($request['failureReason']))
            : OutboundTextMessage::getStatusValueByLabel(trim($request['status']));

        // Update the text message with the provided network code, failure reason, and status
        $textMessage->update([
            'network_code' => $request['networkCode'] ?? NULL,
            'failure_reason' => $request['failureReason'] ?? NULL,
            '_status' => $status
        ]);
    }

    public function bulkSMSOptOut(array $request): void
    {
        // Retrieve the profile of the person who has opted out.
        $phoneNumber = phoneNumberPrefix(trim($request['phoneNumber']));
        $profile = Profile::where('telephone', $phoneNumber)->firstOrFail();

        // Get the existing configuration and update the out_bound_text_message field.
        $configuration = json_decode($profile->configuration, true);

        // If the configuration is not an array, initialize it as an empty array.
        if (!is_array($configuration)) {
            $configuration = [];
        }

        // Update the out_bound_text_message field.
        $configuration['out_bound_text_message'] = false;

        // Save the updated configuration back to the profile.
        $profile->update(['configuration' => json_encode($configuration)]);
    }
}
