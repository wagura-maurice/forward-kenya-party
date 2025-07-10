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

        // Dispatch notification
        SendOtpNotification::dispatch($profile, $otp);
        
        return true; // Successfully sent OTP via email (and SMS if uncommented)
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
