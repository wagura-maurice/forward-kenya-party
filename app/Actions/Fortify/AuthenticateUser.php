<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthenticateUser
{
    public function __invoke($request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $cacheKey = 'recaptcha_' . md5($request->input('g-recaptcha-response'));

            try {
                $validationResult = Cache::remember($cacheKey, 60, function () use ($request) {
                    return app(CaptchaValidation::class)->validate($request->input('g-recaptcha-response'));
                });

                Log::info('ReCaptcha validation result', ['result' => $validationResult]);

                if ($validationResult && Hash::check($request->password, $user->password)) {
                    return $user;
                }
            } catch (\Exception $e) {
                Log::error('ReCaptcha validation error', ['error' => $e->getMessage()]);
            }
        }

        throw ValidationException::withMessages([
            Fortify::username() => [trans('auth.failed')],
        ]);
    }
}