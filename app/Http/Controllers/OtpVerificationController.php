<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\OTP\OneTimePasswordServices;

class OtpVerificationController extends Controller
{
    protected $oneTimePasswordServices;

    public function __construct(OneTimePasswordServices $oneTimePasswordServices)
    {
        $this->oneTimePasswordServices = $oneTimePasswordServices;
    }

    /**
     * Send OTP to the provided telephone number
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create a profile array with the required fields for OTP service
            $profile = [
                '_uid' => $request->telephone, // Using telephone as the unique identifier
                'telephone' => $request->telephone,
                'critical' => $request->has('critical') ? $request->critical : true, // Mark as critical by default for registration
            ];
            
            if ($this->oneTimePasswordServices->send($profile)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP has been sent to your telephone number.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'OTP failed to send to your telephone number. Please try again later!'
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP Request Error: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Verify the OTP provided by the user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string',
            'otp' => 'required|string|min:6|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($this->oneTimePasswordServices->verify($request->telephone, $request->otp)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verified successfully.',
                    'verified' => true
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid OTP. Please try again.',
                    'verified' => false
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP Verification Error: ' . $th->getMessage(),
                'verified' => false
            ], 500);
        }
    }
}