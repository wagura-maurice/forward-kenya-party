<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Services\IPN\Mpesa\Daraja\LipaNaMpesaOnlineServices;

class LipaNaMpesaOnlineController extends Controller
{
    public function transact(Request $request, LipaNaMpesaOnlineServices $lipaNaMpesaOnlineServices)
    {
        try {
            $invoice = Invoice::where('_uid', $request->invoice)
                ->with('user.profile')
                ->first();

            if(optional($invoice)->_status && $invoice->_status != Invoice::SETTLED) {
                $data = array_filter([
                    'amount' => (int) (optional($request)->amount ?? $invoice->payable),
                    'telephone' => phoneNumberPrefix(trim(optional($request)->telephone ?? $invoice->user->profile->telephone)),
                    'reference' => strtolower(trim(optional($request)->account ?? $invoice->_uid)),
                    'description' => strtolower(trim(optional($request)->description ?? $invoice->description)),
                    'category' => $invoice->category->slug,
                ]);

                return response()->json(array_filter([
                    'status' => 'info',
                    'message' => 'lipa na mpesa online transaction settlement for invoice #' . $invoice->_uid . ' processing',
                    'data' => $lipaNaMpesaOnlineServices->transact($data)
                ]), 200);
            }

        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());

            return response()->json(array_filter([
                'status' => 'danger',
                'message' => strtoupper(get_class($this) . ' ' . $th->getMessage()),
                'data' => array_filter([])
            ]), 400);
        }
    }
    
    public function query(Request $request, LipaNaMpesaOnlineServices $lipaNaMpesaOnlineServices)
    {
        try {
            return response()->json(array_filter([
                'status' => 'info',
                'message' => 'lipa na mpesa online transaction #' . $request->transaction_id . ' query processing',
                'data' => $lipaNaMpesaOnlineServices->query($request->transaction_id)
            ]), 200);

        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());

            return response()->json(array_filter([
                'status' => 'danger',
                'message' => strtoupper(get_class($this) . ' ' . $th->getMessage()),
                'data' => array_filter([])
            ]), 400);
        }
    }
    
    public function callback(Request $request, LipaNaMpesaOnlineServices $lipaNaMpesaOnlineServices)
    {
        try {
            return response()->json(array_filter([
                'status' => 'info',
                'message' => 'lipa na mpesa online transaction callback processing',
                'data' => $lipaNaMpesaOnlineServices->callback($request->toArray())
            ]), 200);
        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());

            return response()->json(array_filter([
                'status' => 'danger',
                'message' => strtoupper(get_class($this) . ' ' . $th->getMessage()),
                'data' => array_filter([])
            ]), 400);
        }
    }
}
