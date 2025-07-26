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
        return [new WithoutOverlapping($this->profile['_uid'])];
    }
    
    // We'll set the queue in the dispatch method instead of using a property

    protected $profile;
    protected $otp;

    /**
     * Create a new job instance.
     *
     * @param array $profile
     * @param array $otp
     * @return void
     */
    public function __construct(array $profile, array $otp)
    {
        $this->profile = $profile;
        $this->otp = $otp;
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
            Log::info('OTP sent to ' . $this->profile['telephone'] . ': ' . $this->otp['token']);

            // In a production environment, you would send the OTP via SMS
            // For example, using Africa's Talking or another SMS service:
            
            /*
            $message = "Your verification code is: " . $this->otp['token'] . ". Valid for 5 minutes.";
            
            // Send SMS using your preferred service
            $smsService = new SmsService();
            $smsService->send($this->profile['telephone'], $message);
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
     * @param  array  $profile
     * @param  array  $otp
     * @return mixed
     */
    public static function dispatchSync($profile, $otp)
    {
        $job = new static($profile, $otp);
        return $job->handle(); // Run immediately in the current process
    }
}