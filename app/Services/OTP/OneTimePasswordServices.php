<?php
// app/Services/OTP/OneTimePasswordServices.php
namespace App\Services\OTP;

use Illuminate\Support\Carbon;
use App\Jobs\SendOtpNotification;
use Illuminate\Support\Facades\Cache;

class OneTimePasswordServices
{
    public const TTL = 300; // OTP time to live in seconds (5 minutes)
    public const RATE_LIMIT = 60; // Seconds to wait between OTP requests
    public const ATTEMPTS_LIMIT = 5; // Maximum allowed verification attempts

    public function send(array $profile): bool
    {
        // Check for rate limiting to prevent abuse
        if ($this->isRateLimited($profile['_uid'])) {
            throw new \Exception('You must wait a minute before requesting another OTP.');
        }

        // Generate OTP
        $otp = $this->generate($profile['_uid']);

        // Cache the OTP with an expiration time
        Cache::put($this->getCacheKey($profile['_uid']), json_encode($otp), self::TTL);

        // Set rate limit to prevent rapid repeated requests
        $this->setRateLimit($profile['_uid']);

        // Check if we should process this synchronously (for critical operations)
        $isCritical = isset($profile['critical']) && $profile['critical'] === true;
        
        if ($isCritical) {
            // For critical operations, send OTP directly
            $this->sendOtpDirectly($profile, $otp);
        } else {
            // For regular operations, dispatch with high priority
            // Only use this if you have queue workers running
            // SendOtpNotification::dispatch($profile, $otp)->onQueue('high')->afterCommit();
            
            // Since no queue workers are running, send directly for all cases
            $this->sendOtpDirectly($profile, $otp);
        }
        
        return true; // Successfully sent OTP via email (and SMS if uncommented)
    }
    
    /**
     * Send OTP directly without using a job
     *
     * @param array $profile
     * @param array $otp
     * @return bool
     */
    protected function sendOtpDirectly(array $profile, array $otp): bool
    {
        try {
            // Log the OTP for testing purposes
            \Illuminate\Support\Facades\Log::info('OTP sent to ' . $profile['telephone'] . ': ' . $otp['token']);

            // In a production environment, you would send the OTP via SMS
            // For example, using Africa's Talking or another SMS service:
            
            /*
            $message = "Your verification code is: " . $otp['token'] . ". Valid for 5 minutes.";
            
            // Send SMS using your preferred service
            $smsService = new SmsService();
            $smsService->send($profile['telephone'], $message);
            */
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send OTP: ' . $e->getMessage());
            return false;
        }
    }

    public function verify(string $identifier, string $otp): bool
    {
        $cachedOTP = Cache::get($this->getCacheKey($identifier));

        if (!$cachedOTP) {
            throw new \Exception('OTP provided has expired or does not exist. Please try again later!');
        }

        $cachedOTP = json_decode($cachedOTP, true);
        
        if (!Carbon::parse($cachedOTP['expiry'])->isFuture()) {
            Cache::forget($this->getCacheKey($identifier));
            throw new \Exception('OTP has expired.');
        }

        if ($cachedOTP['identifier'] !== $identifier) {
            throw new \Exception('Identifier mismatch.');
        }

        $attemptsKey = $this->getAttemptsKey($identifier);
        $attempts = Cache::get($attemptsKey, 0);

        if ($attempts >= self::ATTEMPTS_LIMIT) {
            Cache::forget($this->getCacheKey($identifier));
            Cache::forget($attemptsKey);
            throw new \Exception("OTP verification attempts exceeded for {$identifier}. Please resend OTP.");
        }

        if ($cachedOTP['token'] === $otp) {
            Cache::forget($this->getCacheKey($identifier));
            Cache::forget($this->getAttemptsKey($identifier)); // Reset attempts counter
            return true;
        } else {
            // log Failed Attempt
            $attemptsKey = $this->getAttemptsKey($identifier);
            Cache::increment($attemptsKey);

            if (Cache::get($attemptsKey) >= self::ATTEMPTS_LIMIT) {
                Cache::forget($this->getCacheKey($identifier)); // Invalidate OTP
                throw new \Exception("OTP verification attempts exceeded for {$identifier}. Please resend OTP.");
            }

            throw new \Exception('Invalid OTP provided.');
        }
    }

    protected function generate(string $identifier): array
    {
        return [
            'token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expiry' => Carbon::parse(REQUEST_TIMESTAMP)->addSeconds(self::TTL)->toDateTimeString(),
            'identifier' => $identifier,
        ];
    }

    protected function getCacheKey(string $identifier): string
    {
        return 'otp_' . $identifier;
    }

    protected function isRateLimited(string $identifier): bool
    {
        return Cache::has($this->getRateLimitKey($identifier));
    }

    protected function setRateLimit(string $identifier): void
    {
        Cache::put($this->getRateLimitKey($identifier), true, self::RATE_LIMIT);
    }

    protected function getRateLimitKey(string $identifier): string
    {
        return 'otp_rate_limit_' . $identifier;
    }

    protected function getAttemptsKey(string $identifier): string
    {
        return 'otp_attempts_' . $identifier;
    }

    public function getRemainingTime(string $identifier): string
    {
        $cachedOTP = Cache::get($this->getCacheKey($identifier));
        
        if (!$cachedOTP) {
            throw new \Exception('OTP provided has expired or does not exist.');
        }

        $remainingSeconds = Carbon::parse(json_decode($cachedOTP)->expiry)->diffInSeconds(Carbon::parse(REQUEST_TIMESTAMP));

        if ($remainingSeconds <= 0) {
            Cache::forget($this->getCacheKey($identifier));
            throw new \Exception('OTP has expired.');
        }

        return sprintf('%d minutes and %d seconds', floor($remainingSeconds / 60), $remainingSeconds % 60);
    }
}
