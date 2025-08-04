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
        // dd($input);

        // Get the profile and citizen IDs if they exist
        $profileId = $user->profile?->id;
        $citizenId = $user->profile?->citizen?->id;

        // Validate the input
        $rules = [
            'profile.surname' => ['required', 'string', 'max:255'],
            'profile.other_name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (count(explode(' ', $value)) < 2) {
                    $fail('The other name must be at least two strings.');
                }
            }],
            'profile.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'profile.photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
            'profile.date_of_birth' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                $age = Carbon::parse($value)->age;
                if ($age < 18 || $age > 120) {
                    $fail('The date of birth must be between 18 and 120 years ago.');
                }
            }],
            'profile.gender' => ['required', 'string', 'in:' . implode(',', array_keys(Gender::getGenderOptions()))],
            'profile.ncpwd_number' => [
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
            'profile.ethnicity_id' => ['required', 'exists:ethnicities,id'],
            'profile.religion_id' => ['required', 'exists:religions,id'],
            'profile.telephone' => [
                'required', 
                'string', 
                'max:20',
                'telephone', 
                function ($attribute, $value, $fail) use ($profileId) {
                    $query = Profile::where('telephone', phoneNumberPrefix($value));
                    if ($profileId) {
                        $query->where('id', '!=', $profileId);
                    }
                    if ($query->exists()) {
                        $fail('The telephone number has already been taken.');
                    }
                }
            ],
            'profile.address_line_1' => ['nullable', 'string', 'max:255'],
            'profile.address_line_2' => ['nullable', 'string', 'max:255'],
            'profile.city' => ['nullable', 'string', 'max:255'],
            'profile.state' => ['nullable', 'string', 'max:255'],
            'profile.country' => ['nullable', 'string', 'max:255'],
        ];

        // Add citizen validation rules if citizen exists or we're creating one
        $citizenRules = [
            'citizen.national_identification_number' => [
                'nullable', 
                'string', 
                'max:50',
                $citizenId 
                    ? Rule::unique('citizens', 'national_identification_number')->ignore($citizenId)
                    : Rule::unique('citizens', 'national_identification_number')
            ],
            'citizen.passport_number' => [
                'nullable', 
                'string', 
                'max:50',
                $citizenId 
                    ? Rule::unique('citizens', 'passport_number')->ignore($citizenId)
                    : Rule::unique('citizens', 'passport_number')
            ],
            'citizen.driver_license_number' => [
                'nullable', 
                'string', 
                'max:50',
                $citizenId 
                    ? Rule::unique('citizens', 'driver_license_number')->ignore($citizenId)
                    : Rule::unique('citizens', 'driver_license_number')
            ],
            'citizen.county_id' => ['required', 'exists:counties,id'],
            'citizen.sub_county_id' => ['required', 'exists:sub_counties,id'],
            'citizen.constituency_id' => ['required', 'exists:constituencies,id'],
            'citizen.ward_id' => ['required', 'exists:wards,id'],
            'citizen.location_id' => ['nullable', 'exists:locations,id'],
            'citizen.village_id' => ['nullable', 'exists:villages,id'],
            'citizen.polling_center_id' => ['nullable', 'exists:polling_centers,id'],
            'citizen.polling_station_id' => ['nullable', 'exists:polling_stations,id'],
            'citizen.polling_stream_id' => ['nullable', 'exists:polling_streams,id']
        ];
        
        $rules = array_merge($rules, $citizenRules);

        $validated = Validator::make($input, $rules, [
            'profile.ethnicity_id.required' => 'The ethnicity field is required.',
            'profile.religion_id.required' => 'The religion field is required.',
            'citizen.county_id.required' => 'The county field is required.',
            'citizen.sub_county_id.required' => 'The sub-county field is required.',
            'citizen.constituency_id.required' => 'The constituency field is required.',
            'citizen.ward_id.required' => 'The ward field is required.',
        ]);
        
        $validated->validateWithBag('updateProfileInformation');

        // Handle photo upload if present
        if (isset($input['profile']['photo'])) {
            $user->updateProfilePhoto($input['profile']['photo']);
        }

        try {
            // Start database transaction
            DB::transaction(function () use ($user, $input) {
                // Update user data
                if ($input['profile']['email'] !== $user->email && $user instanceof MustVerifyEmail) {
                    $this->updateVerifiedUser($user, $input);
                } else {
                    $user->forceFill([
                        'name' => $input['profile']['surname'],
                        'email' => $input['profile']['email'],
                    ])->save();
                }

                // Update or create profile
                $profileData = [
                    'uuid' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'first_name' => explode(' ', $input['profile']['other_name'])[0],
                    'middle_name' => explode(' ', $input['profile']['other_name'])[1],
                    'last_name' => $input['profile']['surname'],
                    'date_of_birth' => Carbon::parse($input['profile']['date_of_birth'])->format('Y-m-d'),
                    'gender' => $input['profile']['gender'],
                    'disability_status' => $input['profile']['ncpwd_number'] ? true : false,
                    'ncpwd_number' => $input['profile']['ncpwd_number'] ?? null,
                    'ethnicity_id' => $input['profile']['ethnicity_id'],
                    'religion_id' => $input['profile']['religion_id'],
                    'telephone' => phoneNumberPrefix($input['profile']['telephone']),
                    'address_line_1' => $input['profile']['address_line_1'] ?? null,
                    'address_line_2' => $input['profile']['address_line_2'] ?? null,
                    'city' => $input['profile']['city'] ?? null,
                    'state' => $input['profile']['state'] ?? null,
                    'country' => $input['profile']['country'] ?? 'Kenya',
                ];

                if ($user->profile) {
                    $user->profile->update($profileData);
                } else {
                    $user->profile()->create($profileData);
                }

                // Update or create citizen record
                $citizenData = [
                    'user_id' => $user->id,
                    'county_id' => $input['citizen']['county_id'],
                    'sub_county_id' => $input['citizen']['sub_county_id'],
                    'constituency_id' => $input['citizen']['constituency_id'],
                    'ward_id' => $input['citizen']['ward_id'],
                    'location_id' => $input['citizen']['location_id'],
                    'village_id' => $input['citizen']['village_id'],
                    'polling_center_id' => $input['citizen']['polling_center_id'],
                    'polling_station_id' => $input['citizen']['polling_station_id'],
                    'polling_stream_id' => $input['citizen']['polling_stream_id'],
                    'national_identification_number' => $input['citizen']['national_identification_number'] ?? null,
                    'passport_number' => $input['citizen']['passport_number'] ?? null,
                    'driver_license_number' => $input['citizen']['driver_license_number'] ?? null,
                ];

                if ($user->citizen) {
                    $user->citizen->update($citizenData);
                } else {
                    $user->citizen()->create(array_merge($citizenData, ['uuid' => generateUniqueMembershipNumber()]));
                }
            });

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
        } catch (\Throwable $th) {            
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['profile']['surname'],
            'email' => $input['profile']['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}