<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Profile;
use App\Models\Member;
use App\Models\Gender;
use App\Models\SpecialInterestGroup;
use App\Models\County;
use App\Models\Constituency;
use App\Models\Ward;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Logout;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\PasswordReset;
use App\Services\OTP\OneTimePasswordServices;
use App\Contracts\IppmsServiceInterface;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Authentication\SignInRequest;
use App\Http\Requests\Authentication\SignUpRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Http\Requests\Authentication\ForgotPasswordRequest;

class AuthenticationController extends Controller
{
    protected $oneTimePasswordServices;
    protected $ippmsService;

    public function __construct(OneTimePasswordServices $oneTimePasswordServices)
    {
        $this->oneTimePasswordServices = $oneTimePasswordServices;
        $this->ippmsService = app(\App\Services\IppmsService::class);
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

    /**
     * Verify the OTP for the given telephone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOTP(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'telephone' => 'required|string|telephone',
            'otp' => [
                'required',
                'string',
                'size:6',
                'regex:/^[0-9]+$/' // Ensure OTP contains only digits
            ],
        ]);

        try {
            // Verify OTP with rate limiting
            $verificationResult = $this->oneTimePasswordServices->verify(
                $validated['telephone'],
                $validated['otp']
            );

            if ($verificationResult) {
                // Clear any existing OTP after successful verification
                $this->oneTimePasswordServices->clearOtp($validated['telephone']);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verified successfully.',
                    'verified' => true
                ], 200);
            }

            // Handle failed verification
            $attemptsRemaining = $this->oneTimePasswordServices->getRemainingAttempts($validated['telephone']);
            
            if ($attemptsRemaining <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maximum OTP attempts reached. Please request a new OTP.',
                    'attempts_remaining' => 0,
                    'verified' => false
                ], 429); // 429 Too Many Requests
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP. Please try again.',
                'attempts_remaining' => $attemptsRemaining,
                'verified' => false
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'verified' => false
            ], 422);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('OTP Verification Error: ' . $e->getMessage(), [
                'telephone' => $validated['telephone'] ?? null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to verify OTP at this time. Please try again later.',
                'verified' => false
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

    /**
     * Custom registration endpoint that doesn't auto-login the user
     * User must complete IPPMS OTP verification before being allowed to login
     */
    public function registerWithoutLogin(Request $request)
    {
        $request->validate([
            'surname' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (!preg_match('/^[a-zA-Z]+$/', $value)) {
                    $fail('The ' . $attribute . ' must be a valid surname (letters only).');
                }
            }],
            'other_name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $names = explode(' ', $value);
                if (count($names) < 1 || count($names) > 3) {
                    $fail('The ' . $attribute . ' must be a human name in one to three parts.');
                }
                foreach ($names as $name) {
                    if (!preg_match('/^[a-zA-Z]+$/', $name)) {
                        $fail('The ' . $attribute . ' must contain only letters.');
                    }
                }
            }],
            'telephone' => ['required', 'string', 'max:20', 'telephone', 'unique:profiles,telephone'],
            'identification_type' => ['required', 'string', 'in:national_identification_number,passport_number'],
            'identification_number' => ['required', 'string', 'max:50', 'unique:members,national_identification_number'],
            'party_membership_number' => ['required', 'string', 'max:50', 'unique:members,party_membership_number'],
            'date_of_birth' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                $age = Carbon::parse($value)->age;
                if ($age < 18 || $age > 120) {
                    $fail('The date of birth must be between 18 and 120 years ago.');
                }
            }],
            'special_interest_groups' => ['nullable', 'array', function ($attribute, $value, $fail) {
                if (!is_array($value) || !empty(array_diff($value, array_keys(SpecialInterestGroup::getSpecialInterestGroupOptions())))) {
                    $fail('The special interest groups must be an array and must only contain valid options.');
                }
            }],
            'gender' => ['required', 'string', 'in:' . implode(',', array_keys(Gender::getGenderOptions()))],
            'ethnicity_id' => ['required', 'exists:ethnicities,id'],
            'religion_id' => ['required', 'exists:religions,id'],
            'disability_status' => ['required', 'boolean'],
            'ncpwd_number' => [
                'nullable', 
                'string', 
                'max:50',
                function ($attribute, $value, $fail) use ($request) {
                    if (($request->disability_status ?? false) && empty($value)) {
                        $fail('The NCPWD number is required when disability status is "Yes".');
                    } elseif ($value && Member::where('ncpwd_number', $value)->exists()) {
                        $fail('The NCPWD number has already been taken.');
                    }
                }
            ],
            'county_id' => ['required', 'exists:counties,id'],
            'constituency_id' => ['required', 'exists:constituencies,id'],
            'ward_id' => ['required', 'exists:wards,id'],
            'enlisting_date' => ['required', 'date', 'before_or_equal:today'],
            'terms' => ['required', 'accepted', 'in:1,true']
        ], [
            'ncpwd_number.required' => 'The NCPWD number is required when disability status is "Yes".',
            'terms.accepted' => 'You must accept the terms and conditions to register.'
        ]);

        try {
            $input = $request->all();
            $input['telephone'] = phoneNumberPrefix(str_replace(' ', '', $input['telephone']));

            // Validate location hierarchy
            $errors = [];
            if (isset($input['county_id'], $input['sub_county_id'])) {
                $subCounty = \App\Models\SubCounty::find($input['sub_county_id']);
                if (!$subCounty || $subCounty->county_id != $input['county_id']) {
                    $errors['sub_county_id'] = ['Selected sub-county does not belong to the selected county.'];
                }
            }
            if (isset($input['county_id'], $input['constituency_id'])) {
                $constituency = \App\Models\Constituency::find($input['constituency_id']);
                if (!$constituency || $constituency->county_id != $input['county_id']) {
                    $errors['constituency_id'] = ['Selected constituency does not belong to the selected county.'];
                }
            }
            if (isset($input['constituency_id'], $input['ward_id'])) {
                $ward = \App\Models\Ward::find($input['ward_id']);
                if (!$ward || $ward->constituency_id != $input['constituency_id']) {
                    $errors['ward_id'] = ['Selected ward does not belong to the selected constituency.'];
                }
            }
            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }

            // Create user without auto-login
            $user = DB::transaction(function () use ($input) {
                return tap(User::create([
                    'name' => $input['surname'],
                    'email' => null,
                    'telephone' => $input['telephone'],
                    'email_verified_at' => null,
                    'password' => Hash::make($input['identification_number']),
                ]), function (User $user) use ($input) {
                    $user->assignRole('member');
            
                    $nameParts = explode(' ', $input['other_name'], 2);
            
                    Profile::create([
                        'uuid' => Str::uuid()->toString(),
                        'user_id' => $user->id,
                        'first_name' => $nameParts[0] ?? null,
                        'middle_name' => $nameParts[1] ?? null,
                        'last_name' => $input['surname'],
                        'gender' => $input['gender'],
                        'date_of_birth' => Carbon::parse($input['date_of_birth'])->format('Y-m-d'),
                        'telephone' => $input['telephone'],
                        'address_line_1' => null,
                        'address_line_2' => null,
                        'city' => null,
                        'state' => null,
                    ]);
            
                    Member::create([
                        'uuid' => Str::uuid()->toString(),
                        'user_id' => $user->id,
                        'county_id' => $input['county_id'],
                        'constituency_id' => $input['constituency_id'],
                        'ward_id' => $input['ward_id'],
                        'special_interest_groups' => $input['special_interest_groups'] ?? null,
                        'disability_status' => (bool)$input['disability_status'],
                        'ncpwd_number' => (bool)$input['disability_status'] ? $input['ncpwd_number'] : null,
                        'ethnicity_id' => $input['ethnicity_id'],
                        'religion_id' => $input['religion_id'],
                        'passport_number' => $input['identification_type'] === 'passport_number' 
                            ? $input['identification_number'] 
                            : null,
                        'national_identification_number' => $input['identification_type'] === 'national_identification_number'
                            ? $input['identification_number']
                            : null,
                        'party_membership_number' => $input['party_membership_number'],
                        'is_synced' => false
                    ]);
                });
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful. Please complete IPPMS verification to activate your account.',
                'data' => [
                    'member_id' => $user->member->id,
                    'user_id' => $user->id
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            \Log::error('Registration error: ' . $th->getMessage(), [
                'input' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during registration.'
            ], 500);
        }
    }

    /**
     * Login user after successful IPPMS verification
     */
    public function loginAfterIppmsVerification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::find($request->user_id);
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found.'
                ], 404);
            }

            // Check if user has completed IPPMS verification
            if (!$user->member || !$user->member->is_synced) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please complete IPPMS verification before logging in.'
                ], 403);
            }

            // Log the user in using Sanctum token
            $token = $user->createToken('ippms-verification')->plainTextToken;

            // Update last login details
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'data' => [
                    'user' => $user->load('member', 'profile'),
                    'token' => $token
                ]
            ], 200);

        } catch (\Throwable $th) {
            \Log::error('Login after IPPMS verification error: ' . $th->getMessage(), [
                'user_id' => $request->user_id,
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to log in: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Check membership status via IPPMS
     */
    public function checkMembershipStatus(Request $request)
    {
        $request->validate([
            'identification_number' => 'required|string|size:8',
        ]);

        try {
            $response = $this->ippmsService->getMembershipStatus(
                $request->identification_number,
                1 // document type: 1 = national ID
            );

            if ($response['success']) {
                $data = $response['data'] ?? [];
                $status = $data['status'] ?? null;

                return response()->json([
                    'status' => 'success',
                    'membership_status' => $status,
                    'message' => $status === 'Accepted' 
                        ? 'Your ID is eligible for registration.' 
                        : 'Your ID is not eligible for registration.',
                    'data' => $data
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $response['error'] ?? 'Failed to check membership status.'
            ], 400);

        } catch (\Throwable $th) {
            \Log::error('Membership status check error: ' . $th->getMessage(), [
                'identification_number' => $request->identification_number
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to verify membership status at this time. Please try again later.'
            ], 500);
        }
    }

    /**
     * Request IPPMS confirmation code for legal registration
     */
    public function requestIppmsConfirmationCode(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        try {
            $member = Member::find($request->member_id);
            
            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member not found.'
                ], 404);
            }

            $documentNo = $member->national_identification_number ?? $member->passport_number;
            $phoneNumber = phoneNumberPrefix($member->user->profile->telephone ?? null);
            $firstName = $member->user->profile->first_name ?? null;

            if (!$documentNo || !$phoneNumber || !$firstName) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing required information for IPPMS confirmation code request.'
                ], 400);
            }

            $response = $this->ippmsService->getConfirmationCode(
                $documentNo,
                1, // document type: 1 = national ID
                $phoneNumber,
                $firstName
            );

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Confirmation code sent successfully.',
                    'data' => $response['data'] ?? []
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $response['error'] ?? 'Failed to request confirmation code.'
            ], 400);

        } catch (\Throwable $th) {
            \Log::error('IPPMS confirmation code request error: ' . $th->getMessage(), [
                'member_id' => $request->member_id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to request confirmation code at this time. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get current user's member data
     */
    public function getCurrentMember(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated.'
                ], 401);
            }

            $member = $user->member;
            
            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member record not found.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $member->toArray()
            ], 200);

        } catch (\Throwable $th) {
            \Log::error('Get current member error: ' . $th->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch member data.'
            ], 500);
        }
    }

    /**
     * Complete IPPMS registration with OTP
     */
    public function completeIppmsRegistration(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'otp' => 'required|string|size:5',
        ]);

        try {
            $member = Member::with(['user.profile'])->find($request->member_id);
            
            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member not found.'
                ], 404);
            }

            if (!$member->user || !$member->user->profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User profile data not found. Please contact support.'
                ], 404);
            }

            \Log::info('Preparing IPPMS registration data', [
                'member_id' => $member->id,
                'county_id' => $member->county_id,
                'constituency_id' => $member->constituency_id,
                'ward_id' => $member->ward_id
            ]);

            // Prepare IPPMS registration data
            try {
                $county = County::find($member->county_id);
                $constituency = Constituency::find($member->constituency_id);
                $ward = Ward::find($member->ward_id);
            } catch (\Throwable $th) {
                \Log::error('Error finding location data: ' . $th->getMessage());
                throw new \Exception('Failed to retrieve location data. Please check your county, constituency, and ward selections.');
            }

            if (!$county || !$constituency || !$ward) {
                \Log::error('Location data not found', [
                    'county' => $county ? $county->id : null,
                    'constituency' => $constituency ? $constituency->id : null,
                    'ward' => $ward ? $ward->id : null
                ]);
                throw new \Exception('One or more location data (county, constituency, or ward) not found. Please contact support.');
            }

            try {
                $genderName = Gender::getGenderName($member->user->profile->gender);
                $sex = $genderName === 'Male' ? 'M' : 'F';
            } catch (\Throwable $th) {
                \Log::error('Error getting gender: ' . $th->getMessage());
                throw new \Exception('Failed to determine gender from profile data.');
            }

            $data = [
                'registrationId' => $request->registration_id ?? null, // Will be passed from frontend
                'confirmationCode' => $request->otp,
                'partyCode' => '872',
                'birthDate' => $member->user->profile->date_of_birth?->format('Y-m-d') ?? '',
                'sex' => $sex,
                'regDate' => now()->format('Y-m-d'),
                'countyCode' => $county ? $county->code : '',
                'constituencyCode' => $constituency ? $constituency->code : '',
                'wardCode' => $ward ? $ward->code : '',
                'pwd' => (bool)$member->disability_status,
                'membershipNo' => $member->party_membership_number ?? '',
            ];

            if ($data['pwd'] && !empty($member->ncpwd_number)) {
                $data['ncpwdNumber'] = $member->ncpwd_number;
            }

            \Log::info('Calling IPPMS registerMember', [
                'data' => $data
            ]);

            $response = $this->ippmsService->registerMember($data);

            \Log::info('IPPMS registration response', [
                'response' => $response,
                'member_id' => $member->id,
                'data' => $data
            ]);

            if ($response['success']) {
                // Update member as synced
                $member->update([
                    'is_synced' => true,
                    'metadata' => array_merge($member->metadata ?? [], [
                        'ippms_synced_at' => now()->toIso8601String(),
                        'ippms_response' => $response['data'] ?? []
                    ])
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Registration completed successfully. You are now a legally registered party member.',
                    'data' => $response['data'] ?? []
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $response['error'] ?? 'Failed to complete IPPMS registration.'
            ], 400);

        } catch (\Throwable $th) {
            \Log::error('IPPMS registration completion error: ' . $th->getMessage(), [
                'member_id' => $request->member_id,
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to complete IPPMS registration: ' . $th->getMessage()
            ], 500);
        }
    }
}
