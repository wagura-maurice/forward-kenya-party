<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Profile;
use App\Models\Member;
use App\Models\Gender;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MembersImport implements ToCollection, WithHeadingRow
{
    protected $updateExisting;
    protected $importReport = [];
    protected $originalHeaders = [];

    public function __construct($updateExisting = true)
    {
        $this->updateExisting = $updateExisting;
    }

    public function collection(Collection $rows)
    {
        // Store original headers for report generation
        if ($rows->isNotEmpty()) {
            $this->originalHeaders = array_keys($rows->first()->toArray());
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index
            
            try {
                $result = $this->importMember($row, $rowNumber);
                $this->importReport[] = [
                    'row_number' => $rowNumber,
                    'status' => 'success',
                    'message' => 'Member imported successfully',
                    'data' => $row->toArray()
                ];
            } catch (\Exception $e) {
                $this->importReport[] = [
                    'row_number' => $rowNumber,
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'data' => $row->toArray()
                ];
            }
        }
    }

    protected function importMember($row, $rowNumber)
    {
        // dd($row);
        // Validate required fields
        $validator = Validator::make($row->toArray(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id_number' => 'required|max:50',
            'email_address' => 'nullable|email|max:255',
            'phone_number' => 'required|max:20',
            'gender' => 'required|string|in:male,female',
            'date_of_birth' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'county' => 'required|string|max:255',
            'constituency' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'polling_center' => 'nullable|string|max:255',
            'disability_status' => 'nullable|boolean',
            'ncpwd_number' => 'required_if:disability_status,true|max:50',
        ]);

        // Add custom validation for location names and uniqueness checks
        $validator->after(function ($validator) use ($row) {
            $this->validateLocationExists($validator, 'county', $row['county'], 'counties');
            $this->validateLocationExists($validator, 'constituency', $row['constituency'], 'constituencies');
            $this->validateLocationExists($validator, 'ward', $row['ward'], 'wards');
            
            // Validate uniqueness for various fields
            $this->validateFieldUniqueness($validator, $row, 'email_address', 'users', 'email', 'The email');
            $this->validateFieldUniqueness($validator, $row, 'national_id_number', 'members', 'national_identification_number', 'The National ID number');
            $this->validateFieldUniqueness($validator, $row, 'phone_number', 'profiles', 'telephone', 'The phone number');
            $this->validateFieldUniqueness($validator, $row, 'ncpwd_number', 'members', 'ncpwd_number', 'The NCPWD number');
        });

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            throw new \Exception("Validation failed: " . implode(', ', $errors));
        }

        // Convert gender to chromosome code
        $genderCode = null;
        if (!empty($row['gender'])) {
            $genderCode = strtolower($row['gender']) === 'male' ? Gender::MALE : Gender::FEMALE;
        }

        // Check if member already exists by National ID Number
        $existingMember = Member::where('national_identification_number', $row['national_id_number'])->first();
        
        if ($existingMember && !$this->updateExisting) {
            throw new \Exception("Member with National ID Number '{$row['national_id_number']}' already exists and update_existing is false");
        }

        // Create or update user
        if ($existingMember) {
            $user = $existingMember->user;
            $member = $existingMember;
            $profile = $user->profile;
        } else {
            // Create new user
            $user = User::create([
                'name' => trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']),
                'email' => $row['email_address'] ?? null,
                'password' => Hash::make('password'), // Default password, should be changed
            ]);

            // Create profile
            $profile = Profile::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'] ?? null,
                'last_name' => $row['last_name'],
                'gender' => $genderCode,
                'date_of_birth' => $row['date_of_birth'] ?? null,
                'telephone' => $row['phone_number'] ?? null,
            ]);

            // Create member
            $member = Member::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'national_identification_number' => $row['national_id_number'],
                'party_membership_number' => generateUniqueNumber('FKP', Member::class, 'party_membership_number'),
                'disability_status' => filter_var($row['disability_status'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'ncpwd_number' => $row['ncpwd_number'] ?? null,
            ]);
        }

        // Update existing records if updating
        if ($this->updateExisting) {
            // Update user
            $user->update([
                'name' => trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']),
                'email' => $row['email_address'] ?? $user->email,
            ]);

            // Update profile
            if ($profile) {
                $profile->update([
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?? null,
                    'last_name' => $row['last_name'],
                    'gender' => $genderCode ?? $profile->gender,
                    'date_of_birth' => $row['date_of_birth'] ?? $profile->date_of_birth,
                    'telephone' => $row['phone_number'] ?? $profile->telephone,
                ]);
            }

            // Update member
            $member->update([
                'disability_status' => filter_var($row['disability_status'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'ncpwd_number' => $row['ncpwd_number'] ?? $member->ncpwd_number,
            ]);
        }

        // Handle location data (this would need proper implementation with actual location IDs)
        $this->handleLocationData($member, $row);
    }

    protected function validateLocationExists($validator, $field, $value, $table)
    {
        if (!empty($value)) {
            $exists = \DB::table($table)->whereRaw('LOWER(name) = ?', [strtolower($value)])->exists();
            if (!$exists) {
                $validator->errors()->add($field, "The {$field} '{$value}' does not exist in the system.");
            }
        }
    }

    protected function validateFieldUniqueness($validator, $row, $field, $table, $dbField, $fieldLabel)
    {
        if (!empty($row[$field])) {
            $query = \DB::table($table);
            
            // For existing members, allow updates to same record
            if (isset($row['national_id_number'])) {
                // This is an update - allow same field value for existing member
                // No need to check for duplicates when updating existing member
                return;
            }
            
            // For new members, check for duplicates and ownership
            $query->where($dbField, $row[$field]);
            $exists = $query->exists();
            
            if ($exists) {
                // Check if existing record belongs to the same person
                $existingRecord = $query->first();
                $isSamePerson = $this->isSamePerson($existingRecord, $row, $table);
                
                if (!$isSamePerson) {
                    // Different person - prevent creation
                    $validator->errors()->add($field, "The {$fieldLabel} '{$row[$field]}' is already registered in the system.");
                }
                // If same person, allow update (will be handled by update logic)
            }
        }
    }

    protected function isSamePerson($existingRecord, $newRow, $table)
    {
        // For different tables, use different matching logic
        switch ($table) {
            case 'users':
                // For email, check if existing user has same name
                return $existingRecord->name === trim($newRow['first_name'] . ' ' . ($newRow['middle_name'] ?? '') . ' ' . $newRow['last_name']);
                
            case 'profiles':
                // For phone, check if existing profile has same user_id (same person)
                return isset($newRow['user_id']) && $existingRecord->user_id == $newRow['user_id'];
                
            case 'members':
                // For national_id and ncpwd_number, check if same member
                return isset($newRow['national_id_number']) && $existingRecord->national_identification_number == $newRow['national_id_number'];
                
            default:
                return false;
        }
    }

    protected function handleLocationData($member, $row)
    {
        $locationData = [];
        
        // 1. Find county by name
        if (!empty($row['county'])) {
            $county = \DB::table('counties')
                ->whereRaw('LOWER(name) = ?', [strtolower($row['county'])])
                ->first();
            if ($county) {
                $locationData['county_id'] = $county->id;
            }
        }
        
        // 2. Find constituency by name within county
        if (!empty($row['constituency']) && isset($locationData['county_id'])) {
            $constituency = \DB::table('constituencies')
                ->whereRaw('LOWER(name) = ?', [strtolower($row['constituency'])])
                ->where('county_id', $locationData['county_id'])
                ->first();
            if ($constituency) {
                $locationData['constituency_id'] = $constituency->id;
            }
        }
        
        // 3. Find ward by name within constituency (fallback to county if constituency not found)
        if (!empty($row['ward'])) {
            $wardQuery = \DB::table('wards')
                ->whereRaw('LOWER(name) = ?', [strtolower($row['ward'])]);
                
            if (isset($locationData['constituency_id'])) {
                $wardQuery->where('constituency_id', $locationData['constituency_id']);
            } elseif (isset($locationData['county_id'])) {
                $wardQuery->where('county_id', $locationData['county_id']);
            }
            
            $ward = $wardQuery->first();
            if ($ward) {
                $locationData['ward_id'] = $ward->id;
            }
        }
        
        // 4. Find polling center by name within ward (fallback to county if ward not found)
        if (!empty($row['polling_center'])) {
            $pollingCenterQuery = \DB::table('polling_centers')
                ->whereRaw('LOWER(name) = ?', [strtolower($row['polling_center'])]);
                
            if (isset($locationData['ward_id'])) {
                $pollingCenterQuery->where('ward_id', $locationData['ward_id']);
            } elseif (isset($locationData['constituency_id'])) {
                $pollingCenterQuery->where('constituency_id', $locationData['constituency_id']);
            } elseif (isset($locationData['county_id'])) {
                $pollingCenterQuery->where('county_id', $locationData['county_id']);
            }
            
            $pollingCenter = $pollingCenterQuery->first();
            if ($pollingCenter) {
                $locationData['polling_center_id'] = $pollingCenter->id;
            }
        }
        
        // 5. Update the member with the found IDs
        if (!empty($locationData)) {
            $member->update($locationData);
        }
    }
    
    public function getImportReport()
    {
        return $this->importReport;
    }

    public function generateReportCsv()
    {
        $headers = array_merge($this->originalHeaders, ['Import Status', 'Import Message']);
        $csvContent = implode(',', $headers) . "\n";

        foreach ($this->importReport as $report) {
            $rowData = $report['data'];
            $rowData['Import Status'] = $report['status'];
            $rowData['Import Message'] = $report['message'];
            
            // Escape CSV values
            $csvRow = [];
            foreach ($headers as $header) {
                $value = $rowData[$header] ?? '';
                // Escape commas and quotes in CSV values
                if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
                    $value = '"' . str_replace('"', '""', $value) . '"';
                }
                $csvRow[] = $value;
            }
            $csvContent .= implode(',', $csvRow) . "\n";
        }

        return $csvContent;
    }

    public function getSuccessCount()
    {
        return count(array_filter($this->importReport, fn($r) => $r['status'] === 'success'));
    }

    public function getFailureCount()
    {
        return count(array_filter($this->importReport, fn($r) => $r['status'] === 'failed'));
    }
}
