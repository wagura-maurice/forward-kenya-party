<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\IAN\AfricaIsTalkingServices;

class AfricaIsTalkingController extends Controller
{
    protected $AT;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->AT = new AfricaIsTalkingServices;
    }

    public function atDeliveryReports(Request $request) {
        try {
            // Validate that the request values
            $validatedData = Validator::make($request->all(), [
                'phoneNumber' => 'required|string|telephone',
                'failureReason' => 'nullable|string',
                'retryCount' => 'nullable|integer',
                'id' => 'required|string',
                'status' => 'required|string',
                'networkCode' => 'nullable|string',
            ])->validate();

            // Process the delivery report using the validated data
            return $this->AT->deliveryReports($validatedData);
        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());
        }
    }

    public function atBulkSMSOptOut(Request $request) {
        try {
            // Validate that the request values
            $validatedData = Validator::make($request->all(), [
                'phoneNumber' => 'required|string|telephone',
                'shortCode' => 'nullable|string',
                'keyword' => 'nullable|string',
                'updateType' => 'nullable|string',
            ])->validate();
            
            return $this->AT->bulkSMSOptOut($validatedData);
        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());
        }
    }
}
