<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CaptchaValidation
{
    public function validate($captchaResponse)
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $captchaResponse,
        ]);

        $result = $response->json();

        Log::info('ReCaptcha validation result', $result);

        if (!$result['success']) {
            Log::warning('ReCaptcha validation failed', $result);
            return false;
        }

        Log::info('ReCaptcha validation successful', [
            'score' => $result['score'] ?? 'N/A',
            'action' => $result['action'] ?? 'N/A',
        ]);

        return true;
    }
}