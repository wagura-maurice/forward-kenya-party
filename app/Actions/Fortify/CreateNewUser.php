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
        dd($input);
        \Log::info(print_r($input, true));
        
        // Validate the input
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20', 'telephone'],
            'id_type' => ['required', 'string', 'in:national_identification_number,passport_number'],
            'id_number' => ['required', 'string', 'max:50'],
            'party_membership_number' => ['required', 'string', 'max:50', function ($attribute, $value, $fail) use ($input) {
                if ($value != $input['id_number']) {
                    $fail('The party membership number must be the same as the Identification number.');
                }
            }],
            'date_of_birth' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                if (Carbon::parse($value)->age < 18) {
                    $fail('The date of birth must be at least 18 years ago.');
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
                    }
                }
            ],
            'postal_address' => ['required', 'string', 'max:255'],
            'county_id' => ['required', 'exists:counties,id'],
            'constituency_id' => ['required', 'exists:constituencies,id'],
            'ward_id' => ['required', 'exists:wards,id'],
            'enlisting_date' => ['required', 'date', 'before_or_equal:today'],
            'recruiting_person_name' => ['required', 'string', 'max:255'],
            'signature_member' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'recruiting_person_signature' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'terms' => ['required', 'accepted', 'in:1,true']
        ], [
            'ncpwd_number.required' => 'The NCPWD number is required when disability status is "Yes".',
            'signature_member.required' => 'Please upload your signature.',
            'recruiting_person_signature.required' => 'Please upload the recruiting person\'s signature.',
            'terms.accepted' => 'You must accept the terms and conditions to register.'
        ])->validate();

        // dd($validated);

        // Validate location hierarchy
        $this->validateLocationHierarchy($validated);

        // Start database transaction
        return DB::transaction(function () use ($validated) {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => optional($validated)['email'] ?? null,
                'password' => Hash::make($validated['password'] ?? 'Qwerty123!'),
                'email_verified_at' => optional($validated)['email'] ? now() : NULL,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            $user->assignRole('citizen');

            // Create the user's profile
            Profile::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'first_name' => explode(' ', $validated['name'])[0],
                'middle_name' => count(explode(' ', $validated['name'])) > 1 ? explode(' ', $validated['name'])[1] : null,
                'last_name' => count(explode(' ', $validated['name'])) > 2 ? explode(' ', $validated['name'])[2] : null,
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'disability_status' => $validated['disability_status'],
                'ncpwd_number' => $validated['disability_status'] !== 'false' ? $validated['ncpwd_number'] : null,
                'ethnicity_id' => $validated['ethnicity_id'],
                'religion_id' => $validated['religion_id'],
                'telephone' => phoneNumberPrefix($validated['telephone']),
                'address_line_1' => explode("\n", $validated['postal_address'])[0],
                'address_line_2' => count(explode("\n", $validated['postal_address'])) > 1 ? explode("\n", $validated['postal_address'])[1] : null,
                'city' => count(explode("\n", $validated['postal_address'])) > 1 ? explode("\n", $validated['postal_address'])[1] : null,
                'state' => count(explode("\n", $validated['postal_address'])) > 2 ? explode("\n", $validated['postal_address'])[2] : null,
            ]);

            Citizen::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'county_id' => $validated['county_id'] ?? null,
                'sub_county_id' => $validated['sub_county_id'] ?? null,
                'constituency_id' => $validated['constituency_id'] ?? null,
                'ward_id' => $validated['ward_id'] ?? null,
                $validated['id_type'] === 'national_identification_number' ? 'national_identification_number' : 'passport_number' => $validated['id_number'],
            ]);

            return $user;
        });
    }
}
