<?php
// app/Providers/FortifyServiceProvider.php
namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Rules\RecaptchaRule;
use Illuminate\Support\Facades\RateLimiter;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Customize the login validation
        Fortify::loginView(function () {
            return inertia('Auth/Login');
        });

        // Custom authentication using either email or telephone
        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
                // 'g-recaptcha-response' => ['required', new RecaptchaRule()],
            ]);
            
            $login = trim($request->input('login'));
            
            // Check if the input is an email
            if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $login)->first();
                \Log::info('Email login attempt', [
                    'email' => $login,
                    'user_found' => $user ? 'yes' : 'no'
                ]);
            } 
            // Otherwise treat as phone number
            else {
                // Clean and format the phone number
                $phone = phoneNumberPrefix($login);
                \Log::info('Phone login attempt', [
                    'input' => $login,
                    'formatted_phone' => $phone
                ]);
                
                // First try exact match
                $user = User::whereHas('profile', function($q) use ($phone) {
                    $q->where('telephone', $phone);
                })->first();
                
                // If not found, try matching without formatting
                if (!$user) {
                    $user = User::whereHas('profile', function($q) use ($login) {
                        // Remove any non-numeric characters
                        $cleanNumber = preg_replace('/[^0-9]/', '', $login);
                        // If it starts with 0, replace with country code
                        if (str_starts_with($cleanNumber, '0')) {
                            $cleanNumber = '254' . substr($cleanNumber, 1);
                        }
                        // Try with + prefix
                        $q->where('telephone', 'LIKE', '%' . $cleanNumber);
                    })->first();
                }
                
                // Log the query
                \Log::info('Phone query', [
                    'phone' => $phone,
                    'user_found' => $user ? 'yes' : 'no'
                ]);
            }
            
            // Verify password if user found
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    \Log::info('Login successful', ['user_id' => $user->id]);
                    return $user;
                }
                \Log::warning('Password mismatch', ['user_id' => $user->id]);
            } else {
                \Log::warning('User not found', ['login' => $login]);
            }
            
            return null;
        });

        RateLimiter::for('login', function (Request $request) {
            $login = $request->input('login');
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'telephone';
            $throttleKey = Str::transliterate(Str::lower($field.':'.$login.'|'.$request->ip()));

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
