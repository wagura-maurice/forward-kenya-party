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
        // Get the profile and member IDs if they exist
        $profileId = $user->profile?->id;
        $memberId = $user->member?->id;

        // Validate the input
        $rules = [
            'profile.surname' => ['required', 'string', 'max:255'],
            'profile.other_name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (count(explode(' ', $value)) < 2) {
                    $fail('The other name must be at least two strings.');
                }
            }],
            'profile.email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'profile.photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
            'profile.date_of_birth' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                $age = Carbon::parse($value)->age;
                if ($age < 18 || $age > 120) {
                    $fail('The date of birth must be between 18 and 120 years ago.');
                }
            }],
            'profile.gender' => ['required', 'string', 'in:' . implode(',', array_keys(Gender::getGenderOptions()))],
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

        // Add member validation rules if member exists or we're creating one
        $memberRules = [
            'member.national_identification_number' => [
                'nullable', 
                'string', 
                'max:50',
                $memberId 
                    ? Rule::unique('members', 'national_identification_number')->ignore($memberId)
                    : Rule::unique('members', 'national_identification_number')
            ],
            'member.passport_number' => [
                'nullable', 
                'string', 
                'max:50',
                $memberId 
                    ? Rule::unique('members', 'passport_number')->ignore($memberId)
                    : Rule::unique('members', 'passport_number')
            ],
            'member.county_id' => ['required', 'exists:counties,id'],
            'member.sub_county_id' => ['nullable', 'exists:sub_counties,id'],
            'member.constituency_id' => ['required', 'exists:constituencies,id'],
            'member.ward_id' => ['required', 'exists:wards,id'],
            'member.location_id' => ['nullable', 'exists:locations,id'],
            'member.village_id' => ['nullable', 'exists:villages,id'],
            'member.polling_center_id' => ['nullable', 'exists:polling_centers,id'],
            'member.polling_station_id' => ['nullable', 'exists:polling_stations,id'],
            'member.polling_stream_id' => ['nullable', 'exists:polling_streams,id'],
            'member.special_interest_groups' => ['nullable', 'array'],
            'member.disability_status' => ['required', 'boolean'],
            'member.ncpwd_number' => [
                'nullable', 
                'string', 
                'max:50',
                function ($attribute, $value, $fail) use ($input, $memberId) {
                    if ($value) {
                        $query = \App\Models\Member::where('ncpwd_number', $value);
                        if ($memberId) {
                            $query->where('id', '!=', $memberId);
                        }
                        if ($query->exists()) {
                            $fail('The NCPWD number has already been taken.');
                        }
                    }
                }
            ],
            'member.ethnicity_id' => ['required', 'exists:ethnicities,id'],
            'member.religion_id' => ['required', 'exists:religions,id']
        ];
        
        $rules = array_merge($rules, $memberRules);

        $validated = Validator::make($input, $rules, [
            'member.ethnicity_id.required' => 'The ethnicity field is required.',
            'member.religion_id.required' => 'The religion field is required.',
            'member.county_id.required' => 'The county field is required.',
            'member.sub_county_id.required' => 'The sub-county field is required.',
            'member.constituency_id.required' => 'The constituency field is required.',
            'member.ward_id.required' => 'The ward field is required.',
            'member.location_id.required' => 'The location field is required.',
            'member.village_id.required' => 'The village field is required.',
            'member.polling_center_id.required' => 'The polling center field is required.',
            'member.polling_station_id.required' => 'The polling station field is required.',
            'member.polling_stream_id.required' => 'The polling stream field is required.',
        ]);
        
        $validated->validateWithBag('updateProfileInformation');

        // Handle photo upload if present
        if (isset($input['profile']['photo'])) {
            $user->updateProfilePhoto($input['profile']['photo']);
        }

        try {
            // Start database transaction
            DB::transaction(function () use ($user, $input) {                
                if ($input['profile']['email'] !== $user->email && $user instanceof MustVerifyEmail) {
                    $this->updateVerifiedUser($user, $input);
                } else {
                    $user->forceFill([
                        'name' => $input['profile']['surname'],
                        'email' => $input['profile']['email'],
                    ])->save();
                }

                // Update or create profile
                $nameParts = explode(' ', $input['profile']['other_name'], 2);
                $profileData = [
                    'uuid' => $user->profile->uuid ?? Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'first_name' => $nameParts[0],
                    'middle_name' => $nameParts[1] ?? null,
                    'last_name' => $input['profile']['surname'],
                    'date_of_birth' => Carbon::parse($input['profile']['date_of_birth'])->format('Y-m-d'),
                    'gender' => $input['profile']['gender'],
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

                // Update or create member record
                $memberData = [
                    'user_id' => $user->id,
                    'county_id' => $input['member']['county_id'],
                    'sub_county_id' => $input['member']['sub_county_id'],
                    'constituency_id' => $input['member']['constituency_id'],
                    'ward_id' => $input['member']['ward_id'],
                    'location_id' => $input['member']['location_id'],
                    'village_id' => $input['member']['village_id'],
                    'polling_center_id' => $input['member']['polling_center_id'],
                    'polling_station_id' => $input['member']['polling_station_id'],
                    'polling_stream_id' => $input['member']['polling_stream_id'],
                    'special_interest_groups' => $input['member']['special_interest_groups'] ?? null,
                    'disability_status' => $input['member']['disability_status'],
                    'ncpwd_number' => $input['member']['ncpwd_number'] ?? null,
                    'ethnicity_id' => $input['member']['ethnicity_id'],
                    'religion_id' => $input['member']['religion_id'],
                    'national_identification_number' => $input['member']['national_identification_number'] ?? null,
                    'passport_number' => $input['member']['passport_number'] ?? null,
                ];

                if ($user->member) {
                    $user->member->update($memberData);
                } else {
                    $user->member()->create(array_merge($memberData, ['uuid' => generateUniqueMembershipNumber(), 'party_membership_number' => generateUniqueMembershipNumber()]));
                }
            });

            if ($user->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile updated successfully!',
                    'user' => $user->fresh()->load('profile', 'member')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Profile update failed!',
            ], 500);
        } catch (\Throwable $th) {  
            // throw $th;
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