<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Gender;
use App\Models\Citizen;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate location hierarchy for county, sub_county, constituency, and ward.
     * Throws ValidationException if any child does not belong to the selected parent.
     *
     * @param array $input
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLocationHierarchy(array $input)
    {
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
    }

    /**
     * Create a new user
     *
     * @param array $input
     * @return \App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(array $input)
    {      
        // dd($input);

        // Add this
        app(CaptchaValidation::class)->validate($input['g-recaptcha-response']);
          
        // Validate the input
        $validated = Validator::make($input, [
            'surname' => ['required', 'string', 'max:255'],
            'other_name' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20', 'telephone'],
            'identification_type' => ['required', 'string', 'in:national_identification_number,passport_number'],
            'identification_number' => ['required', 'string', 'max:50'],
            'party_membership_number' => ['required', 'string', 'max:50', function ($attribute, $value, $fail) {
                if (Citizen::where('uuid', $value)->exists()) {
                    $fail('The party membership number has already been taken.');
                }
            }],
            'date_of_birth' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                $age = Carbon::parse($value)->age;
                if ($age < 18 || $age > 120) {
                    $fail('The date of birth must be between 18 and 120 years ago.');
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
                function ($attribute, $value, $fail) use ($input) {
                    if (($input['disability_status'] ?? false) && empty($value)) {
                        $fail('The NCPWD number is required when disability status is "Yes".');
                    } elseif ($value && Profile::where('ncpwd_number', $value)->exists()) {
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
        ])->validate();

        // Validate location hierarchy
        $this->validateLocationHierarchy($validated);

        // Start database transaction
        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['surname'],
                'email' => null,
                'telephone' => $input['telephone'],
                'email_verified_at' => null,
                'password' => Hash::make($input['identification_number']),
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]), function (User $user) use ($input) {
                // Assign citizen role
                $user->assignRole('citizen');
        
                // Split other_name into first and middle names
                $nameParts = explode(' ', $input['other_name'], 2);
        
                // Create profile
                Profile::create([
                    'uuid' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'first_name' => $nameParts[0] ?? null,
                    'middle_name' => $nameParts[1] ?? null,
                    'last_name' => $input['surname'],
                    'gender' => $input['gender'],
                    'date_of_birth' => Carbon::parse($input['date_of_birth'])->format('Y-m-d'),
                    'disability_status' => (bool)$input['disability_status'],
                    'ncpwd_number' => (bool)$input['disability_status'] ? $input['ncpwd_number'] : null,
                    'ethnicity_id' => $input['ethnicity_id'],
                    'religion_id' => $input['religion_id'],
                    'telephone' => $input['telephone'],
                    'address_line_1' => null,
                    'address_line_2' => null,
                    'city' => null,
                    'state' => null,
                ]);
        
                // Create citizen record
                Citizen::create([
                    'uuid' => $input['party_membership_number'], // Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'county_id' => $input['county_id'],
                    'constituency_id' => $input['constituency_id'],
                    'ward_id' => $input['ward_id'],
                    'passport_number' => $input['identification_type'] === 'passport_number' 
                        ? $input['identification_number'] 
                        : null,
                    'national_identification_number' => $input['identification_type'] === 'national_identification_number'
                        ? $input['identification_number']
                        : null,
                    'party_membership_number' => $input['party_membership_number']
                ]);
            });
        });
    }
}
