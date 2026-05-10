<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Gender;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Use the same validation as Fortify CreateNewUser
        $validator = Validator::make($request->all(), [
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
                $age = \Carbon\Carbon::parse($value)->age;
                if ($age < 18 || $age > 120) {
                    $fail('The date of birth must be between 18 and 120 years ago.');
                }
            }],
            'special_interest_groups' => ['nullable', 'array', function ($attribute, $value, $fail) {
                if (!is_array($value) || !empty(array_diff($value, array_keys(\App\Models\SpecialInterestGroup::getSpecialInterestGroupOptions())))) {
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
                    } elseif ($value && \App\Models\Member::where('ncpwd_number', $value)->exists()) {
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

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user using Fortify action
            $createNewUser = new CreateNewUser();
            $input = $request->all();

            // dd($input);
            
            // Add dummy captcha response for admin-created members (bypass captcha)
            $input['g-recaptcha-response'] = 'admin_bypass';
            
            $user = $createNewUser->create($input);

            return response()->json([
                'success' => true,
                'message' => 'Member created successfully.',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
