<?php
// tests/Feature/OtpTest.php
namespace Tests\Feature;

use Exception;
use Tests\TestCase;
use InvalidArgumentException;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use App\Services\OTP\OneTimePasswordServices;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OtpTest extends TestCase
{
    // No need for RefreshDatabase trait since we're using array cache

    private OneTimePasswordServices $otpService;
    private string $testTelephone;

    protected function setUp(): void
    {
        $this->testTelephone = phoneNumberPrefix('0721362497'); // Updated to include country code for better validation
        parent::setUp();
        
        // Use array cache driver for testing
        config(['cache.default' => 'array']);
        
        $this->otpService = new OneTimePasswordServices();
        
        // Clear all relevant cache keys
        $this->clearCacheKeys();
    }

    /**
     * Clear all cache keys that might be used by the OTP service
     */
    protected function clearCacheKeys(): void
    {
        // Clear all possible cache keys that might be used by the OTP service
        $identifier = $this->testTelephone;
        $hashedId = md5($identifier);
        
        $cacheKeys = [
            "otp:{$hashedId}",
            "otp:rate_limit:{$hashedId}",
            "otp:attempts:{$hashedId}",
            "otp:expiration:{$hashedId}",
            "otp:rate_limit:{$hashedId}:expiry",
            // Legacy key formats
            "otp_{$identifier}",
            "otp_rate_limit_{$identifier}",
            "otp_attempts_{$identifier}",
            "otp_expiration_{$identifier}",
        ];
        
        foreach (array_unique($cacheKeys) as $key) {
            Cache::forget($key);
        }
        
        // Clear rate limit by setting a new rate limit that expires now
        $expiredTime = now()->subSecond();
        Cache::put("otp:rate_limit:{$hashedId}", true, $expiredTime);
        Cache::put("otp:rate_limit:{$hashedId}:expiry", $expiredTime->timestamp, $expiredTime);
        
        // Also clear any legacy rate limit keys
        Cache::forget("otp_rate_limit_{$identifier}");
    }

    #[Test]
    /**
     * Helper method to get cached OTP for testing purposes
     */
    protected function getCachedOtpForTesting(string $identifier): ?array
    {
        // Try both new and legacy key formats
        $key = 'otp:' . md5($identifier);
        $legacyKey = 'otp_' . $identifier;
        
        return Cache::get($key) ?? Cache::get($legacyKey);
    }
    
    public function it_can_send_otp_to_telephone_number(): void
    {
        // Clear any existing OTP data
        $this->clearCacheKeys();
        
        // Send OTP
        $result = $this->otpService->send($this->testTelephone);
        $this->assertTrue($result, 'OTP sending should be successful');
        
        // Verify OTP was stored in cache by trying to verify it
        $cachedOtp = $this->getCachedOtpForTesting($this->testTelephone);
        
        if ($cachedOtp === null) {
            // Debug: Check what's actually in the cache
            $possibleKeys = [
                'otp:' . md5($this->testTelephone),
                'otp_' . $this->testTelephone,
            ];
            
            $debugInfo = [];
            foreach ($possibleKeys as $key) {
                $debugInfo[$key] = Cache::get($key);
            }
            
            $this->fail("OTP not found in cache. Debug info: " . json_encode($debugInfo, JSON_PRETTY_PRINT));
        }
        
        $this->assertIsArray($cachedOtp, 'OTP should be an array');
        $this->assertArrayHasKey('token', $cachedOtp, 'OTP should have a token key');
        $this->assertMatchesRegularExpression('/^\d{6}$/', $cachedOtp['token'], 'OTP should be 6 digits');
        
        // Verify rate limiting is working by trying to send another OTP
        try {
            $this->otpService->send($this->testTelephone);
            $this->fail('Expected rate limiting to prevent sending a second OTP');
        } catch (\Exception $e) {
            // Expected exception - rate limiting is working
            $this->assertStringContainsString('wait', strtolower($e->getMessage()));
        }
        
        // Verify expiration was set by checking remaining time
        $remainingTime = $this->otpService->getRemainingTime($this->testTelephone);
        $this->assertGreaterThan(0, $remainingTime, 'Remaining time should be greater than 0');
        
        // Output the OTP for debugging purposes
        echo "\nOTP sent to {$this->testTelephone}: " . $cachedOtp['token'] . "\n";
    }

    #[Test]
    public function it_throws_exception_for_invalid_telephone_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->otpService->send('invalid-number');
    }

    #[Test]
    public function it_throws_exception_for_empty_telephone_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Telephone number is required');
        $this->otpService->send('');
    }

    #[Test]
    public function it_enforces_rate_limiting(): void
    {
        $this->clearCacheKeys();
        
        // First attempt should succeed
        $this->assertTrue($this->otpService->send($this->testTelephone));
        
        // Verify rate limit is set
        $rateLimitKey = 'otp:rate_limit:' . md5($this->testTelephone);
        $this->assertTrue(Cache::has($rateLimitKey), 'Rate limit should be set after sending OTP');
        
        // Check that rate limiting is in effect by trying to send another OTP
        try {
            $this->otpService->send($this->testTelephone);
            $this->fail('Expected rate limiting to prevent sending a second OTP');
        } catch (\Exception $e) {
            // Expected exception - rate limiting is working
            $this->assertStringContainsString('wait', strtolower($e->getMessage()));
        }
    }

    #[Test]
    public function it_can_verify_valid_otp(): void
    {
        $this->clearCacheKeys();
        
        // Send OTP and get the token
        $this->otpService->send($this->testTelephone);
        $cachedOtp = $this->getCachedOtpForTesting($this->testTelephone);
        
        if ($cachedOtp === null) {
            $this->fail('OTP was not stored in cache');
        }
        
        $otpToken = $cachedOtp['token'];
        
        // Verify with correct OTP
        $result = $this->otpService->verify($this->testTelephone, $otpToken);
        $this->assertTrue($result, 'Verification with correct OTP should succeed');
        
        // OTP should be invalidated after successful verification
        $this->assertNull(
            $this->getCachedOtpForTesting($this->testTelephone),
            'OTP should be invalidated after successful verification'
        );
    }

    #[Test]
    public function it_rejects_invalid_otp(): void
    {
        $this->clearCacheKeys();
        
        $this->otpService->send($this->testTelephone);
        
        $this->assertFalse(
            $this->otpService->verify($this->testTelephone, 'wrong-otp'),
            'Verification with wrong OTP should fail'
        );
    }

    #[Test]
    public function it_increments_attempts_on_failed_verification(): void
    {
        $this->clearCacheKeys();
        
        $this->otpService->send($this->testTelephone);
        
        $initialAttempts = $this->otpService->getRemainingAttempts($this->testTelephone);
        $this->otpService->verify($this->testTelephone, 'wrongotp');
        $newAttempts = $this->otpService->getRemainingAttempts($this->testTelephone);
        
        $this->assertEquals(
            $initialAttempts - 1, 
            $newAttempts,
            'Failed verification should decrement remaining attempts'
        );
    }

    #[Test]
    public function it_enforces_max_attempts(): void
    {
        $this->clearCacheKeys();
        
        $this->otpService->send($this->testTelephone);
        
        // Exhaust all attempts
        for ($i = 0; $i < OneTimePasswordServices::ATTEMPTS_LIMIT; $i++) {
            $this->otpService->verify($this->testTelephone, 'wrong' . $i);
        }
        
        // Next attempt should throw an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('exceeded the maximum');
        $this->otpService->verify($this->testTelephone, '123456');
    }

    #[Test]
    public function it_returns_correct_remaining_attempts(): void
    {
        $this->otpService->send($this->testTelephone);
        $remaining = $this->otpService->getRemainingAttempts($this->testTelephone);
        
        $this->assertEquals(OneTimePasswordServices::ATTEMPTS_LIMIT, $remaining);
    }

    #[Test]
    public function it_returns_correct_remaining_time(): void
    {
        $this->otpService->send($this->testTelephone);
        $remainingTime = $this->otpService->getRemainingTime($this->testTelephone);
        
        $this->assertNotNull($remainingTime);
        $this->assertLessThanOrEqual(OneTimePasswordServices::TTL, $remainingTime);
        $this->assertGreaterThan(0, $remainingTime);
    }
}