<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Logout;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Services\OTP\OneTimePasswordServices;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Authentication\SignInRequest;
use App\Http\Requests\Authentication\SignUpRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Http\Requests\Authentication\ForgotPasswordRequest;

class AuthenticationController extends Controller
{
    protected $oneTimePasswordServices;

    public function __construct(OneTimePasswordServices $oneTimePasswordServices)
    {
        $this->oneTimePasswordServices = $oneTimePasswordServices;
    }
    
    public function signUp(SignUpRequest $request)
    {        
        try {
            $user = User::create([
                'name' => $request->validated()['name'],
                'email' => $request->validated()['email'],
                'password' => bcrypt($request->validated()['password']),
            ]);
            
            Profile::create([
                '_uid' => generateUID(Profile::class),
                'user_id' => $user->id,
                'telephone' => $request->validated()['telephone'],
                'first_name' => $request->validated()['first_name'],
            ]);

            $user->assignRole($request->validated()['role']);

            // Send email verification notification
            $user->sendEmailVerificationNotification();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User successfully signed up. A verification link has been sent to your email address.',
                'data' => $user
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profile sign up failed: ' . $th->getMessage()
            ], 500);
        }
    }

    public function signIn(SignInRequest $request)
    {
        try {
            if ($request->authenticate()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User successfully signed in',
                    'data' => [
                        'user' => $request->user(),
                        'accessToken' => $request->user()->createToken('sanctumToken', $request->user()->abilities()->toArray())->plainTextToken
                    ]
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sign in failed. Please try again.'
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sign in failed due to validation errors.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sign in failed: ' . $th->getMessage()
            ], 500);
        }
    }

    public function requestOTP(Request $request)
    {
        $request->validate([
            'telephone' => 'required|string|telephone',
        ]);

        try {
            $this->oneTimePasswordServices->send($request->telephone);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent to your telephone number.'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP Request Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'telephone' => 'required|string|telephone',
            'otp' => 'required|string|min:6|max:6',
        ]);

        try {
            if ($this->oneTimePasswordServices->verify($request->telephone, $request->otp)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verified successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid OTP. Please try again.'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP Verification Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $status = Password::sendResetLink($request->only('email'));
            $message = __($status);

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error sending password reset link: ' . $th->getMessage()
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $status = Password::reset($request->validated(), function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            });

            return response()->json([
                'status' => 'success',
                'message' => __($status)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error resetting password: ' . $th->getMessage()
            ], 500);
        }
    }

    public function requestEmailVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No user found with the provided email.'
                ], 404);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Email address is already verified.'
                ], 200);
            }

            $user->sendEmailVerificationNotification();

            return response()->json([
                'status' => 'success',
                'message' => 'Email verification notification sent.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error sending email verification: ' . $th->getMessage()
            ], 500);
        }
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        try {
            if ($request->fulfill()) {
                event(new Verified($request->user()));
                return response()->json([
                    'status' => 'success',
                    'message' => 'User profile successfully verified'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'info',
                    'message' => 'User profile already verified'
                ], 200);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
                'message' => 'Verification failed due to validation errors'
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Verification failed: ' . $th->getMessage()
            ], 500);
        }
    }

    public function signOut(Request $request)
    {
        try {
            if ($request->user()->currentAccessToken()->delete()) {
                event(new Logout('api', $request->user()));
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'User successfully signed out.'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Sign out failed. Please try again later.'
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error during sign out: ' . $th->getMessage()
            ], 500);
        }
    }
}
