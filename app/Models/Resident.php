<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resident extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const REGISTERED = 1;
    const ACTIVE = 2;
    const RESETTLED = 3;
    const REJECTED = 4;
    
    // Entry point constants
    const AIRPORT = 0;
    const BORDER = 1;
    const CROSSING = 2;
    const ETC = 3;
    const OTHER_ENTRY = 4;
    
    // Reason for residence constants
    const PERSECUTION = 0;
    const WAR = 1;
    const NATURAL_DISASTER = 2;
    const OTHER_REASON = 3;
    
    // Length of stay constants
    const SHORT_TERM = 0;
    const MEDIUM_TERM = 1;
    const LONG_TERM = 2;

    /**
     * Get all status options with their labels
     */
    public static function statusLabels(): array
    {
        return [
            self::PENDING => 'Pending',
            self::REGISTERED => 'Registered',
            self::ACTIVE => 'Active',
            self::RESETTLED => 'Resettled',
            self::REJECTED => 'Rejected',
        ];
    }
    
    /**
     * Get all entry point options with their labels
     */
    public static function entryPointLabels(): array
    {
        return [
            self::AIRPORT => 'Airport',
            self::BORDER => 'Border',
            self::CROSSING => 'Crossing',
            self::ETC => 'Etc',
            self::OTHER_ENTRY => 'Other',
        ];
    }
    
    /**
     * Get all reason for residence options with their labels
     */
    public static function reasonForResidenceLabels(): array
    {
        return [
            self::PERSECUTION => 'Persecution',
            self::WAR => 'War',
            self::NATURAL_DISASTER => 'Natural Disaster',
            self::OTHER_REASON => 'Other',
        ];
    }
    
    /**
     * Get all length of stay options with their labels
     */
    public static function lengthOfStayLabels(): array
    {
        return [
            self::SHORT_TERM => 'Short-term',
            self::MEDIUM_TERM => 'Medium-term',
            self::LONG_TERM => 'Long-term',
        ];
    }

    /**
     * Get status value by label
     */
    public static function getStatusValueByLabel(string $label): int|false
    {
        return self::getValueByLabel(self::statusLabels(), $label);
    }
    
    /**
     * Get entry point value by label
     */
    public static function getEntryPointValueByLabel(string $label): int|false
    {
        return self::getValueByLabel(self::entryPointLabels(), $label);
    }
    
    /**
     * Get reason for residence value by label
     */
    public static function getReasonForResidenceValueByLabel(string $label): int|false
    {
        return self::getValueByLabel(self::reasonForResidenceLabels(), $label);
    }
    
    /**
     * Get length of stay value by label
     */
    public static function getLengthOfStayValueByLabel(string $label): int|false
    {
        return self::getValueByLabel(self::lengthOfStayLabels(), $label);
    }
    
    /**
     * Helper method to get value by label from an options array
     */
    protected static function getValueByLabel(array $options, string $label): int|false
    {
        $lowerLabel = strtolower($label);
        
        foreach ($options as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'entry_point' => 'integer',
        'arrival_date' => 'datetime',
        'departure_date' => 'datetime',
        'reason_for_residence' => 'integer',
        'length_of_stay' => 'integer',
        'is_featured' => 'boolean',
        'configuration' => 'array',
        'metadata' => 'array',
        '_status' => 'integer',
        'last_verified_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'country_id',
        'region_id',
        'county_id',
        'sub_county_id',
        'constituency_id',
        'ward_id',
        'location_id',
        'village_id',
        'polling_station_id',
        'consulate_id',
        'refugee_center_id',
        'nearest_school',
        'registration_number',
        'passport_number',
        'national_identification_number',
        'driver_license_number',
        'kra_pin_number',
        'nhif_number',
        'nssf_number',
        'shif_number',
        'sha_number',
        'bank_code',
        'bank_branch_code',
        'bank_account_number',
        'bank_account_name',
        'mobile_money_provider_code',
        'mobile_money_account_number',
        'mobile_money_account_name',
        'entry_point',
        'arrival_date',
        'departure_date',
        'reason_for_residence',
        'length_of_stay',
        'configuration',
        'is_featured',
        'metadata',
        '_status',
        'last_verified_at',
        'verified_by',
    ];
    
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\ResidentRequest::class;
    }
    
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ResidentResource::class;
    }
    
    /**
     * Get the validation rules for creating a new resident.
     */
    public static function createRules(): array
    {
        return [
            'uuid' => 'required|uuid|unique:residents,uuid',
            'user_id' => 'required|exists:users,id',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'nullable|exists:regions,id',
            'county_id' => 'nullable|exists:counties,id',
            'sub_county_id' => 'nullable|exists:sub_counties,id',
            'constituency_id' => 'nullable|exists:constituencies,id',
            'ward_id' => 'nullable|exists:wards,id',
            'location_id' => 'nullable|exists:locations,id',
            'village_id' => 'nullable|exists:villages,id',
            'polling_station_id' => 'nullable|exists:polling_stations,id',
            'consulate_id' => 'nullable|exists:consulates,id',
            'refugee_center_id' => 'nullable|exists:refugee_centers,id',
            'nearest_school' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100|unique:residents,registration_number',
            'passport_number' => 'nullable|string|max:100|unique:residents,passport_number',
            'national_identification_number' => 'nullable|string|max:100|unique:residents,national_identification_number',
            'driver_license_number' => 'nullable|string|max:100|unique:residents,driver_license_number',
            'kra_pin_number' => 'nullable|string|max:50|unique:residents,kra_pin_number',
            'nhif_number' => 'nullable|string|max:50|unique:residents,nhif_number',
            'nssf_number' => 'nullable|string|max:50|unique:residents,nssf_number',
            'shif_number' => 'nullable|string|max:50|unique:residents,shif_number',
            'sha_number' => 'nullable|string|max:50|unique:residents,sha_number',
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:residents,bank_account_number',
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:residents,mobile_money_account_number',
            'mobile_money_account_name' => 'nullable|string|max:255',
            'entry_point' => 'required|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'required|date',
            'departure_date' => 'nullable|date|after:arrival_date',
            'reason_for_residence' => 'required|integer|in:' . implode(',', array_keys(self::reasonForResidenceLabels())),
            'length_of_stay' => 'required|integer|in:' . implode(',', array_keys(self::lengthOfStayLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
        ];
    }
    
    /**
     * Get the validation rules for updating a resident.
     */
    public static function updateRules(int $residentId): array
    {
        return [
            'uuid' => 'nullable|uuid|unique:residents,uuid,' . $residentId,
            'user_id' => 'nullable|exists:users,id',
            'country_id' => 'nullable|exists:countries,id',
            'region_id' => 'nullable|exists:regions,id',
            'county_id' => 'nullable|exists:counties,id',
            'sub_county_id' => 'nullable|exists:sub_counties,id',
            'constituency_id' => 'nullable|exists:constituencies,id',
            'ward_id' => 'nullable|exists:wards,id',
            'location_id' => 'nullable|exists:locations,id',
            'village_id' => 'nullable|exists:villages,id',
            'polling_station_id' => 'nullable|exists:polling_stations,id',
            'consulate_id' => 'nullable|exists:consulates,id',
            'refugee_center_id' => 'nullable|exists:refugee_centers,id',
            'nearest_school' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100|unique:residents,registration_number,' . $residentId,
            'passport_number' => 'nullable|string|max:100|unique:residents,passport_number,' . $residentId,
            'national_identification_number' => 'nullable|string|max:100|unique:residents,national_identification_number,' . $residentId,
            'driver_license_number' => 'nullable|string|max:100|unique:residents,driver_license_number,' . $residentId,
            'kra_pin_number' => 'nullable|string|max:50|unique:residents,kra_pin_number,' . $residentId,
            'nhif_number' => 'nullable|string|max:50|unique:residents,nhif_number,' . $residentId,
            'nssf_number' => 'nullable|string|max:50|unique:residents,nssf_number,' . $residentId,
            'shif_number' => 'nullable|string|max:50|unique:residents,shif_number,' . $residentId,
            'sha_number' => 'nullable|string|max:50|unique:residents,sha_number,' . $residentId,
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:residents,bank_account_number,' . $residentId,
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:residents,mobile_money_account_number,' . $residentId,
            'mobile_money_account_name' => 'nullable|string|max:255',
            'entry_point' => 'nullable|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after:arrival_date',
            'reason_for_residence' => 'nullable|integer|in:' . implode(',', array_keys(self::reasonForResidenceLabels())),
            'length_of_stay' => 'nullable|integer|in:' . implode(',', array_keys(self::lengthOfStayLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
        ];
    }
    
    /**
     * Get the user that owns the resident.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country of the resident.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the region of the resident.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the county of the resident.
     */
    
    public function county()
    {
        return $this->belongsTo(County::class, 'county_id');
    }
    
    public function subCounty()
    {
        return $this->belongsTo(SubCounty::class, 'sub_county_id');
    }
    
    public function constituency()
    {
        return $this->belongsTo(Constituency::class, 'constituency_id');
    }
    
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }
    
    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality');
    }
    
    public function consulate()
    {
        return $this->belongsTo(Consulate::class, 'consulate_id');
    }

    public function refugeeCenter()
    {
        return $this->belongsTo(RefugeeCenter::class, 'refugee_center_id');
    }
}
