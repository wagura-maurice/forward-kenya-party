<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class SendOtpNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;
    
    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new WithoutOverlapping($this->telephone)];
    }
    
    /**
     * The telephone number to send the OTP to.
     *
     * @var string
     */
    protected $telephone;

    /**
     * The OTP code to send.
     *
     * @var string
     */
    protected $otpCode;

    /**
     * Create a new job instance.
     *
     * @param string $telephone
     * @param string $otpCode
     * @return void
     */
    public function __construct(string $telephone, string $otpCode)
    {
        $this->telephone = $telephone;
        $this->otpCode = $otpCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Log the OTP for testing purposes
            Log::info('OTP sent to ' . $this->telephone . ': ' . $this->otpCode);

            // In a production environment, you would send the OTP via SMS
            // For example, using Africa's Talking or another SMS service:
            
            /*
            $message = "Your verification code is: " . $this->otpCode . ". Valid for 5 minutes.";
            
            // Send SMS using your preferred service
            $smsService = new SmsService();
            $smsService->send($this->telephone, $message);
            */
            
            // For now, we'll just log it for testing
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Dispatch the job with the given arguments synchronously.
     *
     * @param  string  $telephone
     * @param  string  $otpCode
     * @return mixed
     */
    public static function dispatchNow($telephone, $otpCode)
    {
        return (new static($telephone, $otpCode))->handle();
    }
}