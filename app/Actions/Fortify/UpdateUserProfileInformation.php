<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Gender;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input)
    {
        // Get the profile and citizen IDs if they exist
        $profileId = $user->profile?->id;
        $citizenId = $user->profile?->citizen?->id;

        // Validate the input
        $rules = [
            'surname' => ['required', 'string', 'max:255'],
            'other_name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (count(explode(' ', $value)) < 2) {
                    $fail('The other name must be at least two strings.');
                }
            }],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
            'date_of_birth' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                $age = Carbon::parse($value)->age;
                if ($age < 18 || $age > 120) {
                    $fail('The date of birth must be between 18 and 120 years ago.');
                }
            }],
            'gender' => ['required', 'string', 'in:' . implode(',', array_keys(Gender::getGenderOptions()))],
            'ncpwd_number' => [
                'nullable', 
                'string', 
                'max:50',
                function ($attribute, $value, $fail) use ($input, $profileId) {
                    if ($value) {
                        $query = Profile::where('ncpwd_number', $value);
                        if ($profileId) {
                            $query->where('id', '!=', $profileId);
                        }
                        if ($query->exists()) {
                            $fail('The NCPWD number has already been taken.');
                        }
                    }
                }
            ],
            'ethnicity_id' => ['required', 'exists:ethnicities,id'],
            'religion_id' => ['required', 'exists:religions,id'],
            'telephone' => [
                'required', 
                'string', 
                'max:20', 
                function ($attribute, $value, $fail) use ($profileId) {
                    $query = Profile::where('telephone', $value);
                    if ($profileId) {
                        $query->where('id', '!=', $profileId);
                    }
                    if ($query->exists()) {
                        $fail('The telephone number has already been taken.');
                    }
                }
            ],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
        ];

        // Add citizen validation rules if citizen exists or we're creating one
        if ($citizenId) {
            $rules = array_merge($rules, [
                'national_identification_number' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('citizens', 'national_identification_number')->ignore($citizenId)
                ],
                'passport_number' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('citizens', 'passport_number')->ignore($citizenId)
                ],
                'driver_license_number' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('citizens', 'driver_license_number')->ignore($citizenId)
                ]
            ]);
        } else {
            $rules = array_merge($rules, [
                'national_identification_number' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('citizens', 'national_identification_number')
                ],
                'passport_number' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('citizens', 'passport_number')
                ],
                'driver_license_number' => [
                    'nullable', 
                    'string', 
                    'max:50',
                    Rule::unique('citizens', 'driver_license_number')
                ]
            ]);
        }

        $validated = Validator::make($input, $rules, [
            'ethnicity_id.required' => 'The ethnicity field is required.',
            'religion_id.required' => 'The religion field is required.',
        ]);
        $validated->validateWithBag('updateProfileInformation');

        // Handle photo upload if present
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // try {
            // Start database transaction
            // DB::transaction(function () use ($user, $input) {
                // Update user data
                if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
                    $this->updateVerifiedUser($user, $input);
                } else {
                    $user->forceFill([
                        'name' => $input['surname'],
                        'email' => $input['email'],
                    ])->save();
                }

                // Update or create profile
                $profileData = [
                    'uuid' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'first_name' => explode(' ', $input['other_name'])[0],
                    'middle_name' => explode(' ', $input['other_name'])[1],
                    'last_name' => $input['surname'],
                    'date_of_birth' => Carbon::parse($input['date_of_birth'])->format('Y-m-d'),
                    'gender' => $input['gender'],
                    'disability_status' => $input['ncpwd_number'] ? true : false,
                    'ncpwd_number' => $input['ncpwd_number'] ?? null,
                    'ethnicity_id' => $input['ethnicity_id'],
                    'religion_id' => $input['religion_id'],
                    'telephone' => $input['telephone'],
                    'address_line_1' => $input['address_line_1'] ?? null,
                    'address_line_2' => $input['address_line_2'] ?? null,
                    'city' => $input['city'] ?? null,
                    'state' => $input['state'] ?? null,
                    'country' => $input['country'] ?? 'Kenya',
                ];

                if ($user->profile) {
                    $user->profile->update($profileData);
                } else {
                    $user->profile()->create($profileData);
                }

                // Update or create citizen record
                $citizenData = [
                    'user_id' => $user->id,
                    'national_identification_number' => $input['national_identification_number'] ?? null,
                    'passport_number' => $input['passport_number'] ?? null,
                    'driver_license_number' => $input['driver_license_number'] ?? null,
                ];

                if ($user->citizen) {
                    $user->citizen->update($citizenData);
                } else {
                    $user->citizen()->create(array_merge($citizenData, ['uuid' => generateUniqueMembershipNumber()]));
                }
            // });

            if ($user->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile updated successfully!',
                    'user' => $user->fresh()->load('profile', 'citizen')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Profile update failed!',
            ], 500);
        // }
        // } catch (\Throwable $th) {            
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $th->getMessage(),
        //     ], 500);
        // }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['surname'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}