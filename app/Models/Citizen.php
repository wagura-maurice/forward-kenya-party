<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Citizen extends Model
{
    use HasFactory, SoftDeletes;
    
    // Registration point constants
    const ONLINE = 0;
    const OFFLINE = 1;

    // Status constants
    const PENDING = 0;
    const PROCESSING = 1;
    const PROCESSED = 2;
    const ACCEPTED = 3;
    const REJECTED = 4;
    
    /**
     * Get all status options with their labels
     */
    public static function statusLabels(): array
    {
        return [
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
        ];
    }

    /**
     * Get all registration point options with their labels
     */
    public static function registrationPointLabels(): array
    {
        return [
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
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
     * Get registration point value by label
     */
    public static function getRegistrationPointValueByLabel(string $label): int|false
    {
        return self::getValueByLabel(self::registrationPointLabels(), $label);
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
        'registration_point' => 'integer',
        'is_featured' => 'boolean',
        'configuration' => 'array',
        'metadata' => 'array',
        '_status' => 'integer',
        'last_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
        'registration_point',
        'configuration',
        'is_featured',
        'metadata',
        '_status',
        'last_verified_at',
        'verified_by',
    ];
    
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\CitizenRequest::class;
    }
    
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\CitizenResource::class;
    }
    
    /**
     * Get the validation rules for creating a new citizen.
     */
    public static function createRules(): array
    {
        return [
            'uuid' => 'required|uuid|unique:citizens,uuid',
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
            'registration_number' => 'nullable|string|max:100|unique:citizens,registration_number',
            'passport_number' => 'nullable|string|max:100|unique:citizens,passport_number',
            'national_identification_number' => 'nullable|string|max:100|unique:citizens,national_identification_number',
            'driver_license_number' => 'nullable|string|max:100|unique:citizens,driver_license_number',
            'kra_pin_number' => 'nullable|string|max:50|unique:citizens,kra_pin_number',
            'nhif_number' => 'nullable|string|max:50|unique:citizens,nhif_number',
            'nssf_number' => 'nullable|string|max:50|unique:citizens,nssf_number',
            'shif_number' => 'nullable|string|max:50|unique:citizens,shif_number',
            'sha_number' => 'nullable|string|max:50|unique:citizens,sha_number',
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:citizens,bank_account_number',
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:citizens,mobile_money_account_number',
            'mobile_money_account_name' => 'nullable|string|max:255',
            'registration_point' => 'nullable|integer|in:' . implode(',', array_keys(self::registrationPointLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
        ];
    }
    
    /**
     * Get the validation rules for updating a citizen.
     */
    public static function updateRules(int $citizenId): array
    {
        return [
            'uuid' => 'nullable|uuid|unique:citizens,uuid,' . $citizenId,
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
            'registration_number' => 'nullable|string|max:100|unique:citizens,registration_number,' . $citizenId,
            'passport_number' => 'nullable|string|max:100|unique:citizens,passport_number,' . $citizenId,
            'national_identification_number' => 'nullable|string|max:100|unique:citizens,national_identification_number,' . $citizenId,
            'driver_license_number' => 'nullable|string|max:100|unique:citizens,driver_license_number,' . $citizenId,
            'kra_pin_number' => 'nullable|string|max:50|unique:citizens,kra_pin_number,' . $citizenId,
            'nhif_number' => 'nullable|string|max:50|unique:citizens,nhif_number,' . $citizenId,
            'nssf_number' => 'nullable|string|max:50|unique:citizens,nssf_number,' . $citizenId,
            'shif_number' => 'nullable|string|max:50|unique:citizens,shif_number,' . $citizenId,
            'sha_number' => 'nullable|string|max:50|unique:citizens,sha_number,' . $citizenId,
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:citizens,bank_account_number,' . $citizenId,
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:citizens,mobile_money_account_number,' . $citizenId,
            'mobile_money_account_name' => 'nullable|string|max:255',
            'registration_point' => 'nullable|integer|in:' . implode(',', array_keys(self::registrationPointLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
        ];
    }
    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function county()
    {
        return $this->belongsTo(County::class);
    }
    
    public function sub_county()
    {
        return $this->belongsTo(SubCounty::class);
    }
    
    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }
    
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function polling_center()
    {
        return $this->belongsTo(PollingCenter::class);
    }
    
    public function polling_station()
    {
        return $this->belongsTo(PollingStation::class);
    }

    public function polling_stream()
    {
        return $this->belongsTo(PollingStream::class);
    }
    
    public function consulate()
    {
        return $this->belongsTo(Consulate::class);
    }
    
    public function refugee_center()
    {
        return $this->belongsTo(RefugeeCenter::class);
    }
    
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    
    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the registration point label
     */
    public function getRegistrationPointLabelAttribute(): string
    {
        return self::registrationPointLabels()[$this->registration_point] ?? 'Unknown';
    }
    
    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->_status] ?? 'Unknown';
    }
    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    
    /**
     * Scope a query to only include featured citizens.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope a query to only include verified citizens.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('last_verified_at');
    }
    
    /**
     * Scope a query to only include citizens registered online.
     */
    public function scopeOnlineRegistration($query)
    {
        return $query->where('registration_point', self::ONLINE);
    }
    
    /**
     * Scope a query to only include citizens registered offline.
     */
    public function scopeOfflineRegistration($query)
    {
        return $query->where('registration_point', self::OFFLINE);
    }
    
    /**
     * Scope a query to only include citizens with the given status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('_status', $status);
    }
}
