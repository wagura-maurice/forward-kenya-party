<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Media;
use Ramsey\Uuid\Uuid;
use App\Models\Country;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @return \App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    /**
     * Handle file upload and create media record
     *
     * @param string $fileData
     * @param string $type
     * @return \App\Models\Media
     * @throws \Exception
     */
    protected function handleFileUpload($fileData, $type)
    {
        $fileType = '';
        $originalName = '';
        $fileContents = '';
        $extension = '';
        $fileSize = 0;

        // Handle array input (from frontend file upload)
        if (is_array($fileData)) {
            if (isset($fileData['file']) && $fileData['file'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $fileData['file'];
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileType = $file->getClientMimeType();
                $fileContents = file_get_contents($file->getRealPath());
                $fileSize = $file->getSize();
                
                if ($fileContents === false) {
                    throw new \Exception('Failed to read the uploaded file');
                }
            } else {
                throw new \Exception('Invalid file data format');
            }
        }
        // Handle base64 file
        elseif (is_string($fileData) && preg_match('/^data:(\w+)\/(\w+);base64,/', $fileData, $matches)) {
            $fileType = $matches[1];
            $extension = $matches[2];
            
            $file = explode(',', $fileData, 2);
            $fileContents = base64_decode($file[1]);
            $fileSize = strlen($fileContents);
            
            if ($fileContents === false) {
                throw new \Exception('Invalid base64 file data');
            }
            
            $originalName = uniqid() . '.' . $extension;
        }
        // Handle file path
        elseif (is_string($fileData) && file_exists($fileData)) {
            $fileInfo = pathinfo($fileData);
            $extension = $fileInfo['extension'] ?? '';
            $originalName = $fileInfo['basename'];
            $fileContents = file_get_contents($fileData);
            $fileSize = filesize($fileData);
            $fileType = mime_content_type($fileData);
            
            if ($fileContents === false) {
                throw new \Exception('Failed to read the file from path');
            }
        } else {
            throw new \Exception("Invalid file format for {$type}");
        }
        
        // Determine media type based on file extension and mime type
        $mediaType = $this->determineMediaType($extension, $fileType);
        
        // Store the file in the appropriate directory
        $directory = 'uploads/' . $type . '/' . now()->format('Y/m/d');
        $filename = Str::random(40) . '.' . $extension;
        $fullPath = $directory . '/' . $filename;
        
        // Ensure the directory exists
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory, 0755, true);
        }
        
        // Store the file
        if (!Storage::disk('public')->put($fullPath, $fileContents)) {
            throw new \Exception('Failed to store the uploaded file');
        }
        
        // Prepare metadata
        $metadata = [
            'upload_source' => 'user_registration',
            'document_type' => $type,
            'original_name' => $originalName,
            'uploaded_at' => now()->toDateTimeString(),
            'file_properties' => [
                'size' => $fileSize,
                'mime_type' => $fileType,
                'extension' => $extension
            ]
        ];
        
        // Get the public URL for the file
        $endpointUrl = '';
        try {
            $endpointUrl = app('filesystem')->disk('public')->url($fullPath);
        } catch (\Exception $e) {
            Log::error('Failed to generate URL for file', [
                'file' => $fullPath,
                'error' => $e->getMessage()
            ]);
        }
        
        // Get authenticated user ID or null if not authenticated
        $userId = Auth::check() ? Auth::id() : null;
        
        // Remove 'id' from metadata if present
        if (is_array($metadata) && array_key_exists('id', $metadata)) {
            unset($metadata['id']);
        }

        // Create media record
        return Media::create([
            'uuid' => Uuid::uuid4()->toString(),
            'name' => $originalName,
            'slug' => Str::slug(pathinfo($originalName, PATHINFO_FILENAME)),
            'description' => 'Uploaded ' . $type . ' for user registration',
            'type_id' => $mediaType,
            'category_id' => 1, // Default category
            'user_id' => $userId,
            'endpoint_url' => $endpointUrl,
            'file_type' => $fileType,
            'file_extension' => $extension,
            'file_path' => $fullPath,
            'file_name' => $originalName,
            'file_size' => $fileSize,
            'metadata' => $metadata,
            'approved_at' => now(),
            '_status' => Media::PENDING,
        ]);
    }
    
    /**
     * Format a telephone number to E.164 using libphonenumber-for-php
     *
     * @param string $telephone
     * @param string $countryCode
     * @return string
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function formatTelephone($telephone, $countryCode = '254')
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            // Accept both country code (e.g. 254) and region code (e.g. KE)
            if (is_numeric($countryCode)) {
                // Convert numeric country code to region code
                $regionCode = $phoneUtil->getRegionCodeForCountryCode((int)$countryCode);
            } else {
                $regionCode = $countryCode;
            }
            $numberProto = $phoneUtil->parse($telephone, $regionCode);
            if (!$phoneUtil->isValidNumber($numberProto)) {
                throw new \Illuminate\Validation\ValidationException(
                    Validator::make([], []),
                    null,
                    [
                        'telephone' => ['The phone number provided is not valid for the selected country.']
                    ]
                );
            }
            return $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            throw new \Illuminate\Validation\ValidationException(
                Validator::make([], []),
                null,
                [
                    'telephone' => ['Invalid phone number format.']
                ]
            );
        }
    }

    /**
     * Validate location hierarchy for region, county, sub_county, constituency, ward, location, and village.
     * Throws ValidationException if any child does not belong to the selected parent.
     *
     * @param array $input
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLocationHierarchy(array $input)
    {
        $errors = [];

        // Only check if all relevant IDs are present
        if (isset($input['region_id'], $input['county_id'])) {
            $county = \App\Models\County::find($input['county_id']);
            if (!$county || $county->region_id != $input['region_id']) {
                $errors['county_id'] = ['Selected county does not belong to the selected region.'];
            }
        }
        if (isset($input['county_id'], $input['sub_county_id'])) {
            $subCounty = \App\Models\SubCounty::find($input['sub_county_id']);
            if (!$subCounty || $subCounty->county_id != $input['county_id']) {
                $errors['sub_county_id'] = ['Selected sub-county does not belong to the selected county.'];
            }
        }
        if (isset($input['sub_county_id'], $input['constituency_id'])) {
            $constituency = \App\Models\Constituency::find($input['constituency_id']);
            if (!$constituency || $constituency->sub_county_id != $input['sub_county_id']) {
                $errors['constituency_id'] = ['Selected constituency does not belong to the selected sub-county.'];
            }
        }
        if (isset($input['constituency_id'], $input['ward_id'])) {
            $ward = \App\Models\Ward::find($input['ward_id']);
            if (!$ward || $ward->constituency_id != $input['constituency_id']) {
                $errors['ward_id'] = ['Selected ward does not belong to the selected constituency.'];
            }
        }
        if (isset($input['ward_id'], $input['location_id'])) {
            $location = \App\Models\Location::find($input['location_id']);
            if ($location && $location->ward_id != $input['ward_id']) {
                $errors['location_id'] = ['Selected location does not belong to the selected ward.'];
            }
        }
        if (isset($input['location_id'], $input['village_id'])) {
            $village = \App\Models\Village::find($input['village_id']);
            if ($village && $village->location_id != $input['location_id']) {
                $errors['village_id'] = ['Selected village does not belong to the selected location.'];
            }
        }
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Determine media type based on file extension and mime type
     *
     * @param string $extension
     * @param string $fileType
     * @return int
     */
    protected function determineMediaType($extension, $fileType)
    {
        // Default to 'document' type
        $type = 1; // Assuming 1 is the ID for 'document' type
        
        // Map common file types to media types
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $documentTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
        
        if (in_array(strtolower($extension), $imageTypes)) {
            $type = 2; // Assuming 2 is the ID for 'image' type
        } elseif (in_array(strtolower($extension), $documentTypes)) {
            $type = 1; // Document type
        }
        
        return $type;
    }
    
    /**
     * Create a new user
     *
     * @param array $input
     * @return \App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function create(array $input)
    {
        dd($input);

        Log::info('Registration attempt started', ['input' => array_merge($input, ['password' => '***', 'password_confirmation' => '***'])]);

        try {
            return DB::transaction(function () use ($input) {
                \Log::info('Starting user creation transaction', ['input' => $input]);
                // Validate input
                \Log::info('Validating input', ['input' => $input]);
                $rules = [
                    'name' => 'required|string|max:255',
                    'email' => 'nullable|email|unique:users,email',
                    'password' => 'required|string|min:8|confirmed',
                    'password_confirmation' => 'required|same:password',
                    'first_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'gender' => 'required|string|in:male,female,other,prefer_not_to_say',
                    'telephone' => [
                        'required',
                        'string',
                        'max:30',
                        function ($attribute, $value, $fail) {
                            $digits = preg_replace('/[^0-9+]/', '', $value);
                            $digitCount = strlen(preg_replace('/[^0-9]/', '', $value));
                            if ($digitCount < 8 || $digitCount > 20) {
                                $fail('The phone number must be between 8 and 20 digits long.');
                            }
                            if (!preg_match('/^\+?[0-9\s\-()]+$/', $value)) {
                                $fail('Please enter a valid phone number.');
                            }
                        },
                    ],
                    'address_line_1' => 'required|string|max:255',
                    'address_line_2' => 'nullable|string|max:255',
                    'city' => 'required|string|max:255',
                    'state' => 'required|string|max:255',
                    'date_of_birth' => 'required|date|before:-18 years|after:-120 years',
                    'proof_of_address' => 'required|array',
                    'proof_of_address.file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
                    'proof_of_identity' => 'required|array',
                    'proof_of_identity.file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
                    'terms' => ['required', 'boolean', 'accepted'],
                    'role' => 'required|string|in:citizen,resident,refugee,diplomat,foreigner,guest',
                    'idNumber' => 'required|string|max:255',
                    'nationality' => 'required|string|exists:countries,name',
                    'education_level' => 'nullable|string|max:255',
                    'occupation' => 'nullable|string|max:255',
                    'employer_details' => 'nullable|string|max:1000',
                    'security_question' => 'required|string|max:255',
                    'security_answer' => 'required|string|max:255',
                    'country_id' => 'required|exists:countries,id',
                    'region_id' => 'required|exists:regions,id',
                    'county_id' => 'required|exists:counties,id',
                    'sub_county_id' => 'required|exists:sub_counties,id',
                    'constituency_id' => 'required|exists:constituencies,id',
                    'ward_id' => 'required|exists:wards,id',
                    'location_id' => 'nullable|exists:locations,id',
                    'village_id' => 'nullable|exists:villages,id',
                ];

                // Add role-specific validation
                if (isset($input['role'])) {
                    switch ($input['role']) {
                        case 'citizen':
                            $rules['idNumber'] = 'required|string|max:20|unique:citizens,national_identification_number';
                            $rules['polling_station_id'] = 'nullable|exists:polling_stations,id';
                            break;
                        case 'resident':
                            $rules['idNumber'] = 'required|string|max:20|unique:residents,registration_number';
                            $rules['polling_station_id'] = 'nullable|exists:polling_stations,id';
                            $rules['consulate_id'] = 'nullable|exists:consulates,id';
                            break;
                        case 'refugee':
                            $rules['idNumber'] = 'required|string|max:20|unique:refugees,registration_number';
                            $rules['consulate_id'] = 'nullable|exists:consulates,id';
                            $rules['refugee_center_id'] = 'nullable|exists:refugee_centers,id';
                            $rules['reason_for_refugee'] = 'required|string|max:1000';
                            break;
                        case 'diplomat':
                            $rules['idNumber'] = 'required|string|max:20|unique:diplomats,registration_number';
                            $rules['consulate_id'] = 'required|exists:consulates,id';
                            break;
                        case 'foreigner':
                            $rules['idNumber'] = 'required|string|max:20|unique:foreigners,passport_number';
                            $rules['consulate_id'] = 'nullable|exists:consulates,id';
                            break;
                        case 'guest':
                            // No additional validation needed for guests
                            break;
                    }
                }

                $validator = Validator::make($input, $rules, [
                    'terms.accepted' => 'You must accept the terms and conditions.',
                    'idNumber.required' => 'ID/Passport number is required.',
                    'nationality.exists' => 'Selected nationality is invalid.',
                    'password.confirmed' => 'The password confirmation does not match.',
                    'country_id.exists' => 'Selected country is invalid.',
                    'region_id.exists' => 'Selected region is invalid.',
                    'county_id.exists' => 'Selected county is invalid.',
                    'sub_county_id.exists' => 'Selected sub-county is invalid.',
                    'constituency_id.exists' => 'Selected constituency is invalid.',
                    'ward_id.exists' => 'Selected ward is invalid.',
                    'location_id.exists' => 'Selected location is invalid.',
                    'village_id.exists' => 'Selected village is invalid.',
                ]);

                // Validate location hierarchy
                $this->validateLocationHierarchy($input);

                if ($validator->fails()) {
                    Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
                    throw new ValidationException($validator);
                }

                // Get country data
                $defaultCountry = Country::where('iso_code', 'KE')->firstOrFail();
                $country = Country::whereRaw('LOWER(name) = ?', [strtolower($input['nationality'])])
                    ->with(['consulates', 'refugee_centers'])
                    ->firstOrFail();

                // Handle file uploads
                $proofOfAddressMedia = $this->handleFileUpload($input['proof_of_address'], 'proof_of_address');
                $proofOfIdentityMedia = $this->handleFileUpload($input['proof_of_identity'], 'proof_of_identity');

                // Format telephone
                $telephone = $this->formatTelephone($input['telephone'], $country->phone_code ?? '254');

                // Create user
                $user = User::create([
                    'name' => $input['name'],
                    'email' => $input['email'] ?? null,
                    'password' => Hash::make($input['password']),
                    'email_verified_at' => $input['role'] === 'diplomat' ? now() : null,
                ]);

                // Assign role
                if (!$user->assignRole($input['role'])) {
                    Log::alert('Role assignment failed', ['user_id' => $user->id, 'role' => $input['role']]);
                }

                // Create profile
                $profileData = array_filter([
                    'user_id' => $user->id,
                    'uuid' => (string) Str::uuid(),
                    'telephone' => $telephone,
                    'first_name' => $input['first_name'],
                    'middle_name' => $input['middle_name'] ?? null,
                    'last_name' => $input['last_name'] ?? null,
                    'gender' => $input['gender'],
                    'address_line_1' => $input['address_line_1'],
                    'address_line_2' => $input['address_line_2'] ?? null,
                    'city' => $input['city'],
                    'state' => $input['state'] ?? null,
                    'country' => $input['nationality'],
                    'date_of_birth' => Carbon::parse($input['date_of_birth'])->toDateString(),
                    'education_level' => $input['education_level'] ?? null,
                    'occupation' => $input['occupation'] ?? null,
                    'employer_details' => $input['employer_details'] ?? null,
                    'proof_of_address' => $proofOfAddressMedia->id,
                    'proof_of_identity' => $proofOfIdentityMedia->id,
                    'security_question' => $input['security_question'] ?? null,
                    'security_answer' => $input['security_answer'] ?? null,
                    'kyc_verified' => $input['role'] === 'diplomat',
                    'country_id' => $input['country_id'],
                    'region_id' => $input['region_id'],
                    'county_id' => $input['county_id'],
                    'sub_county_id' => $input['sub_county_id'],
                    'constituency_id' => $input['constituency_id'],
                    'ward_id' => $input['ward_id'],
                    'location_id' => $input['location_id'] ?? null,
                    'village_id' => $input['village_id'] ?? null,
                    'consulate_id' => in_array($input['role'], ['foreigner', 'diplomat', 'refugee']) ? 
                        ($input['consulate_id'] ?? $country->consulates->first()?->id) : null,
                    'refugee_center_id' => $input['role'] === 'refugee' ? 
                        ($input['refugee_center_id'] ?? $country->refugee_centers->first()?->id) : null,
                ]);

                $profile = $user->profile()->create($profileData);

                // Handle role-specific logic
                $this->handleRole($user, array_merge($input, [
                    'default_country' => $defaultCountry,
                    'country' => $country,
                    'reason_for_refugee' => $input['reason_for_refugee'] ?? null,
                    'reason_for_residence' => $input['reason_for_residence'] ?? null,
                ]));

                return $user->load(['profile', 'roles']);
            });
        } catch (\Throwable $th) {
            Log::error('User registration failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'input' => array_merge($input, [
                    'password' => '***',
                    'password_confirmation' => '***',
                    'proof_of_identity' => isset($input['proof_of_identity']) ? '***' : null,
                    'proof_of_address' => isset($input['proof_of_address']) ? '***' : null,
                ])
            ]);
            throw $th;
        }
    }
    
    
    /**
     * Handle role-specific user creation logic.
     *
     * @param  \App\Models\User  $user
     * @param  array  $data
     * @return void
     * @throws \Exception
     */
    protected function handleRole(User $user, array $data): void
    {
        try {
            $role = $data['role'];
            $idNumber = $data['idNumber'];
            
            // Validate role-specific requirements
            $this->validateRoleRequirements($role, $data);
            
            // Assign the role
            $user->assignRole($role);
            
            // Create role-specific record
            $roleData = [
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'country_id' => $data['default_country']->id,
            ];
            
            // Add role-specific fields
            switch ($role) {
                case 'citizen':
                    $roleData['national_identification_number'] = $idNumber;
                    $created = $user->citizen()->create($roleData);
                    \Log::info('Citizen record created', ['created' => $created]);
                    break;
                    
                case 'resident':
                case 'refugee':
                case 'diplomat':
                    $roleData = array_merge($roleData, [
                        'nationality' => $data['country']->name,
                        'consulate_id' => optional($data['country']->consulates->first())->id,
                        'registration_number' => $idNumber,
                    ]);
                    
                    if ($role === 'resident') {
                        $roleData['reason_for_residence'] = $data['reason_for_residence'] ?? null;
                        $created = $user->resident()->create($roleData);
                        \Log::info('Resident record created', ['created' => $created]);
                    } elseif ($role === 'refugee') {
                        $roleData['reason_for_refugee'] = $data['reason_for_refugee'] ?? null;
                        $created = $user->refugee()->create($roleData);
                        \Log::info('Refugee record created', ['created' => $created]);
                    } else {
                        $created = $user->diplomat()->create($roleData);
                        \Log::info('Diplomat record created', ['created' => $created]);
                    }
                    break;
                    
                case 'foreigner':
                    $user->foreigner()->create(array_merge($roleData, [
                        'nationality' => $data['country']->id,
                        'consulate_id' => optional($data['country']->consulates->first())->id,
                        'registration_number' => $idNumber,
                        'passport_number' => $idNumber,
                    ]));
                    break;
                    
                default: // guest
                    $user->guest()->create($roleData);
                    break;
            }
            
        } catch (\Exception $e) {
            /* // Handle unique constraint violations
            if ($e->errorInfo[1] === 1062) {
                throw ValidationException::withMessages([
                    'idNumber' => 'The provided ID/Passport number is already registered.'
                ]);
            } */
            throw $e;
        }
    }
    
    /**
     * Validate role-specific requirements.
     *
     * @param  string  $role
     * @param  array  $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRoleRequirements(string $role, array $data): void
    {
        $rules = [
            'country_id' => ['required', 'exists:countries,id'],
            'region_id' => ['required', 'exists:regions,id'],
            'county_id' => ['required', 'exists:counties,id'],
            'constituency_id' => ['required', 'exists:constituencies,id'],
            'ward_id' => ['required', 'exists:wards,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'profile_photo_path' => ['nullable', 'url'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'security_question' => ['nullable', 'string', 'max:255'],
            'security_answer' => ['nullable', 'string', 'max:255'],
        ];
        
        switch ($role) {
            case 'citizen':
                $rules = array_merge($rules, [
                    'idNumber' => ['required', 'string', 'max:20', 'unique:citizens,national_identification_number'],
                    'polling_station_id' => ['nullable', 'exists:polling_stations,id'],
                ]);
                break;
                
            case 'foreigner':
                $rules = array_merge($rules, [
                    'idNumber' => ['required', 'string', 'max:20', 'unique:foreigners,passport_number'],
                    'consulate_id' => ['nullable', 'exists:consulates,id'],
                ]);
                break;
                
            case 'resident':
                $rules = array_merge($rules, [
                    'idNumber' => ['required', 'string', 'max:20', 'unique:residents,registration_number'],
                    'polling_station_id' => ['nullable', 'exists:polling_stations,id'],
                    'consulate_id' => ['nullable', 'exists:consulates,id'],
                ]);
                break;
                
            case 'refugee':
                $rules = array_merge($rules, [
                    'idNumber' => ['required', 'string', 'max:20', 'unique:refugees,registration_number'],
                    'consulate_id' => ['nullable', 'exists:consulates,id'],
                    'refugee_center_id' => ['nullable', 'exists:refugee_centers,id'],
                ]);
                break;
                
            case 'diplomat':
                $rules = array_merge($rules, [
                    'idNumber' => ['required', 'string', 'max:20', 'unique:diplomats,registration_number'],
                    'consulate_id' => ['required', 'exists:consulates,id'],
                ]);
                
                // Validate consulate exists for the country
                if (empty($data['country']->consulates->first())) {
                    throw ValidationException::withMessages([
                        'nationality' => 'No consulate found for the selected country.'
                    ]);
                }
                break;
                
            case 'guest':
                // No additional validation needed for guests
                break;
        }
        
        if (!empty($rules)) {
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
    }
}
