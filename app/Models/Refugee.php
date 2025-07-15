<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refugee extends Model
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
    
    // Reason for refugee status constants
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
    public static function statusLabels()
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
    public static function entryPointLabels()
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
     * Get all reason for refugee status options with their labels
     */
    public static function reasonForRefugeeLabels()
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
    public static function lengthOfStayLabels()
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
    public static function getStatusValueByLabel(string $label)
    {
        return self::getValueByLabel(self::statusLabels(), $label);
    }
    
    /**
     * Get entry point value by label
     */
    public static function getEntryPointValueByLabel(string $label)
    {
        return self::getValueByLabel(self::entryPointLabels(), $label);
    }
    
    /**
     * Get reason for refugee value by label
     */
    public static function getReasonForRefugeeValueByLabel(string $label)
    {
        return self::getValueByLabel(self::reasonForRefugeeLabels(), $label);
    }
    
    /**
     * Get length of stay value by label
     */
    public static function getLengthOfStayValueByLabel(string $label)
    {
        return self::getValueByLabel(self::lengthOfStayLabels(), $label);
    }
    
    /**
     * Helper method to get value by label from an options array
     */
    protected static function getValueByLabel(array $options, string $label)
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'entry_point' => 'integer',
        'reason_for_refugee' => 'integer',
        'length_of_stay' => 'integer',
        'arrival_date' => 'datetime',
        'departure_date' => 'datetime',
        'last_verified_at' => 'datetime',
        'is_featured' => 'boolean',
        'metadata' => 'array',
        'configuration' => 'array',
        '_status' => 'integer',
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'country_id',
        'county_id',
        'sub_county_id',
        'constituency_id',
        'ward_id',
        'location',
        'village',
        'nearest_school',
        'nationality',
        'consulate_id',
        'refugee_center_id',
        'registration_number',
        'passport_number',
        'national_identification_number',
        'driver_license_number',
        'entry_point',
        'arrival_date',
        'departure_date',
        'reason_for_refugee',
        'length_of_stay',
        'configuration',
        'is_featured',
        'metadata',
        '_status',
        'last_verified_at',
        'verified_by',
        'sha_number',
        'bank_code',
        'bank_branch_code',
        'bank_account_number',
        'bank_account_name',
        'mobile_money_provider_code',
        'mobile_money_account_number',
        'mobile_money_account_name',
    ];
    
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\RefugeeRequest::class;
    }
    
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\RefugeeResource::class;
    }
    
    public static function createRules()
    {
        return [
            'uuid' => 'required|uuid|unique:refugees,uuid',
            'user_id' => 'required|exists:users,id',
            'country_id' => 'required|exists:countries,id',
            'county_id' => 'nullable|exists:counties,id',
            'sub_county_id' => 'nullable|exists:sub_counties,id',
            'constituency_id' => 'nullable|exists:constituencies,id',
            'ward_id' => 'nullable|exists:wards,id',
            'location' => 'nullable|string',
            'village' => 'nullable|string',
            'nearest_school' => 'nullable|string',
            'nationality' => 'required|exists:countries,id',
            'consulate_id' => 'nullable|exists:consulates,id',
            'refugee_center_id' => 'required|exists:refugee_centers,id',
            'registration_number' => 'nullable|string|unique:refugees,registration_number',
            'passport_number' => 'nullable|string|unique:refugees,passport_number',
            'national_identification_number' => 'nullable|string|unique:refugees,national_identification_number',
            'driver_license_number' => 'nullable|string|unique:refugees,driver_license_number',
            'entry_point' => 'nullable|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            'reason_for_refugee' => 'nullable|integer|in:' . implode(',', array_keys(self::reasonForRefugeeLabels())),
            'length_of_stay' => 'nullable|integer|in:' . implode(',', array_keys(self::lengthOfStayLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
            'sha_number' => 'nullable|string|unique:refugees,sha_number',
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|unique:refugees,bank_account_number',
            'bank_account_name' => 'nullable|string|max:100',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|unique:refugees,mobile_money_account_number',
            'mobile_money_account_name' => 'nullable|string|max:100',
        ];
    }
    
    public static function updateRules()
    {
        return [
            'uuid' => 'nullable|uuid|unique:refugees,uuid',
            'user_id' => 'nullable|exists:users,id',
            'country_id' => 'nullable|exists:countries,id',
            'county_id' => 'nullable|exists:counties,id',
            'sub_county_id' => 'nullable|exists:sub_counties,id',
            'constituency_id' => 'nullable|exists:constituencies,id',
            'ward_id' => 'nullable|exists:wards,id',
            'location' => 'nullable|string',
            'village' => 'nullable|string',
            'nearest_school' => 'nullable|string',
            'nationality' => 'nullable|exists:countries,id',
            'consulate_id' => 'nullable|exists:consulates,id',
            'refugee_center_id' => 'nullable|exists:refugee_centers,id',
            'registration_number' => 'nullable|string|unique:refugees,registration_number',
            'passport_number' => 'nullable|string|unique:refugees,passport_number',
            'national_identification_number' => 'nullable|string|unique:refugees,national_identification_number',
            'driver_license_number' => 'nullable|string|unique:refugees,driver_license_number',
            'entry_point' => 'nullable|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            'reason_for_refugee' => 'nullable|integer|in:' . implode(',', array_keys(self::reasonForRefugeeLabels())),
            'length_of_stay' => 'nullable|integer|in:' . implode(',', array_keys(self::lengthOfStayLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
            'sha_number' => 'nullable|string|unique:refugees,sha_number',
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|unique:refugees,bank_account_number',
            'bank_account_name' => 'nullable|string|max:100',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|unique:refugees,mobile_money_account_number',
            'mobile_money_account_name' => 'nullable|string|max:100',
        ];
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    
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
