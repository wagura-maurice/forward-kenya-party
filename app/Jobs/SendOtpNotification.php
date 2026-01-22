<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\OutboundTextMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\IAN\AfricaIsTalkingServices;
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
            $message = "Your Forward Kenya Party application verification code is: " . $this->otpCode . ". Valid for 5 minutes.";
            
            // Send SMS using Africa's Talking service
            $smsService = new AfricaIsTalkingServices();
            $response = $smsService->send($this->telephone, $message);
            
            // Create a record in outbound_text_messages
            OutboundTextMessage::create([
                'uuid' => Str::uuid()->toString(),
                'content' => $message,
                'telephone' => $this->telephone,
                'session_id' => $response['transaction_id'] ?? null,
                'sent_at' => now(),
                'session_amount' => $response['transaction_amount'] ?? 0,
                '_status' => $response['_status'] ?? OutboundTextMessage::STATUS_PENDING,
            ]);
        } catch (\Throwable $th) {
            throw $th;
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