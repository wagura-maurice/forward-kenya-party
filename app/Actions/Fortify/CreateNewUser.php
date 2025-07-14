<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Citizen;
use App\Models\Profile;
use Illuminate\Support\Str;
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
        // Validate the input
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date'],
            'disability_status' => ['nullable', 'string', 'in:none,physical,sensory,mental,intellectual,other'],
            'plwd_number' => [
                'nullable', 
                'string', 
                'max:255', 
                function ($attribute, $value, $fail) use ($input) {
                    if (isset($input['disability_status']) && $input['disability_status'] !== 'none' && empty($value)) {
                        $fail('The PLWD number is required when disability status is selected.');
                    }
                }
            ],
            'ethnicity_id' => ['required', 'exists:ethnicities,id'],
            'religion_id' => ['required', 'exists:religions,id'],
            'national_id' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'county_id' => ['required', 'exists:counties,id'],
            'sub_county_id' => ['required', 'exists:sub_counties,id'],
            'constituency_id' => ['required', 'exists:constituencies,id'],
            'ward_id' => ['required', 'exists:wards,id'],
            'security_question' => ['required', 'string', 'max:255'],
            'security_answer' => ['required', 'string', 'max:255'],
            'terms' => ['required', 'accepted'],
        ], [
            'plwd_number.required' => 'The PLWD number is required when disability status is selected.',
        ])->validate();

        // Validate location hierarchy
        $this->validateLocationHierarchy($input);

        // Start database transaction
        return DB::transaction(function () use ($input) {
            // Create the user
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'email_verified_at' => now(),
                'status' => 'active',
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            $user->assignRole('citizen');

            // Create the user's profile
            Profile::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'first_name' => $input['first_name'],
                'middle_name' => $input['middle_name'] ?? null,
                'last_name' => $input['last_name'],
                'gender' => $input['gender'],
                'date_of_birth' => $input['date_of_birth'],
                'disability_status' => $input['disability_status'],
                'plwd_number' => $input['disability_status'] !== 'none' ? $input['plwd_number'] : null,
                'ethnicity_id' => $input['ethnicity_id'],
                'religion_id' => $input['religion_id'],
                'telephone' => phoneNumberPrefix($input['telephone']),
                'address_line_1' => $input['address_line_1'],
                'address_line_2' => $input['address_line_2'] ?? null,
                'city' => $input['city'],
                'state' => $input['state'],
            ]);

            Citizen::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'county_id' => $input['county_id'],
                'sub_county_id' => $input['sub_county_id'],
                'constituency_id' => $input['constituency_id'],
                'ward_id' => $input['ward_id'],
                'national_identification_number' => $input['national_id'],
            ]);

            return $user;
        });
    }
}
