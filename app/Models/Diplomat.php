<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diplomat extends Model
{
    use HasFactory, SoftDeletes;

    // Diplomatic status constants
    const DIPLOMATIC_STATUS_UNKNOWN = 0;
    const DIPLOMATIC_STATUS_AMBASSADOR = 1;
    const DIPLOMATIC_STATUS_CONSUL = 2;
    const DIPLOMATIC_STATUS_ATTACHE = 3;

    // Diplomatic rank constants
    const DIPLOMATIC_RANK_UNKNOWN = 0;
    const DIPLOMATIC_RANK_FIRST_SECRETARY = 1;
    const DIPLOMATIC_RANK_SECOND_SECRETARY = 2;

    // Diplomatic specialization constants
    const DIPLOMATIC_SPECIALIZATION_UNKNOWN = 0;
    const DIPLOMATIC_SPECIALIZATION_ECONOMICS = 1;
    const DIPLOMATIC_SPECIALIZATION_POLITICS = 2;

    // Status constants
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
    const RETIRED = 3;
    
    // Entry point constants
    const AIRPORT = 0;
    const BORDER = 1;
    const CROSSING = 2;
    const ETC = 3;
    const OTHER_ENTRY = 4;
    
    // Length of stay constants
    const SHORT_TERM = 0;
    const MEDIUM_TERM = 1;
    const LONG_TERM = 2;
    
    public static function diplomaticStatusLabels()
    {
        return [
            self::DIPLOMATIC_STATUS_UNKNOWN => 'Unknown',
            self::DIPLOMATIC_STATUS_AMBASSADOR => 'Ambassador',
            self::DIPLOMATIC_STATUS_CONSUL => 'Consul',
            self::DIPLOMATIC_STATUS_ATTACHE => 'Attaché',
        ];
    }

    public static function diplomaticRankLabels()
    {
        return [
            self::DIPLOMATIC_RANK_UNKNOWN => 'Unknown',
            self::DIPLOMATIC_RANK_FIRST_SECRETARY => 'First Secretary',
            self::DIPLOMATIC_RANK_SECOND_SECRETARY => 'Second Secretary',
        ];
    }

    public static function diplomaticSpecializationLabels()
    {
        return [
            self::DIPLOMATIC_SPECIALIZATION_UNKNOWN => 'Unknown',
            self::DIPLOMATIC_SPECIALIZATION_ECONOMICS => 'Economics',
            self::DIPLOMATIC_SPECIALIZATION_POLITICS => 'Politics',
        ];
    }

    public static function statusLabels()
    {
        return [
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::RETIRED => 'Retired',
        ];
    }
    
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
    
    public static function lengthOfStayLabels()
    {
        return [
            self::SHORT_TERM => 'Short-term',
            self::MEDIUM_TERM => 'Medium-term',
            self::LONG_TERM => 'Long-term',
        ];
    }
    
    public static function getDiplomaticStatusValueByLabel(string $label)
    {
        $diplomaticStatusOptions = self::diplomaticStatusLabels();
        $lowerLabel = strtolower($label);

        foreach ($diplomaticStatusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function getDiplomaticRankValueByLabel(string $label)
    {
        $diplomaticRankOptions = self::diplomaticRankLabels();
        $lowerLabel = strtolower($label);

        foreach ($diplomaticRankOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }
    
    public static function getDiplomaticSpecializationValueByLabel(string $label)
    {
        $diplomaticSpecializationOptions = self::diplomaticSpecializationLabels();
        $lowerLabel = strtolower($label);

        foreach ($diplomaticSpecializationOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function getStatusValueByLabel(string $label)
    {
        return self::getValueByLabel(self::statusLabels(), $label);
    }
    
    public static function getEntryPointValueByLabel(string $label)
    {
        return self::getValueByLabel(self::entryPointLabels(), $label);
    }
    
    public static function getLengthOfStayValueByLabel(string $label)
    {
        return self::getValueByLabel(self::lengthOfStayLabels(), $label);
    }
    
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'entry_point' => 'integer',
        'arrival_date' => 'datetime',
        'departure_date' => 'datetime',
        'diplomatic_role' => 'integer',
        'diplomatic_rank' => 'integer',
        'diplomatic_specialization' => 'integer',
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
     * @var array
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
        'diplomatic_mission',
        'diplomatic_role',
        'diplomatic_rank',
        'diplomatic_specialization',
        'diplomatic_language',
        'contact_person_email',
        'contact_person_phone',
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
        return \App\Http\Requests\API\DiplomatRequest::class;
    }
    
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\DiplomatResource::class;
    }
    
    public static function createRules()
    {
        return [
            'uuid' => 'required|uuid|unique:diplomats,uuid',
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
            'registration_number' => 'nullable|string|max:100|unique:diplomats,registration_number',
            'passport_number' => 'nullable|string|max:100|unique:diplomats,passport_number',
            'national_identification_number' => 'nullable|string|max:100|unique:diplomats,national_identification_number',
            'driver_license_number' => 'nullable|string|max:100|unique:diplomats,driver_license_number',
            'kra_pin_number' => 'nullable|string|max:50|unique:diplomats,kra_pin_number',
            'nhif_number' => 'nullable|string|max:50|unique:diplomats,nhif_number',
            'nssf_number' => 'nullable|string|max:50|unique:diplomats,nssf_number',
            'shif_number' => 'nullable|string|max:50|unique:diplomats,shif_number',
            'sha_number' => 'nullable|string|max:50|unique:diplomats,sha_number',
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:diplomats,bank_account_number',
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:diplomats,mobile_money_account_number',
            'mobile_money_account_name' => 'nullable|string|max:255',
            'entry_point' => 'required|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'required|date',
            'departure_date' => 'nullable|date|after:arrival_date',
            'diplomatic_mission' => 'nullable|string|max:255',
            'diplomatic_role' => 'required|integer|in:' . implode(',', [
                self::DIPLOMATIC_STATUS_UNKNOWN,
                self::DIPLOMATIC_STATUS_AMBASSADOR,
                self::DIPLOMATIC_STATUS_CONSUL,
                self::DIPLOMATIC_STATUS_ATTACHE
            ]),
            'diplomatic_rank' => 'required|integer|in:' . implode(',', [
                self::DIPLOMATIC_RANK_UNKNOWN,
                self::DIPLOMATIC_RANK_FIRST_SECRETARY,
                self::DIPLOMATIC_RANK_SECOND_SECRETARY
            ]),
            'diplomatic_specialization' => 'nullable|string',
            'diplomatic_language' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|phone:AUTO',
            'metadata' => 'nullable|json',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }
    
    public static function updateRules($diplomatId)
    {
        return [
            'uuid' => 'nullable|uuid|unique:diplomats,uuid,' . $diplomatId,
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
            'registration_number' => 'nullable|string|max:100|unique:diplomats,registration_number,' . $diplomatId,
            'passport_number' => 'nullable|string|max:100|unique:diplomats,passport_number,' . $diplomatId,
            'national_identification_number' => 'nullable|string|max:100|unique:diplomats,national_identification_number,' . $diplomatId,
            'driver_license_number' => 'nullable|string|max:100|unique:diplomats,driver_license_number,' . $diplomatId,
            'kra_pin_number' => 'nullable|string|max:50|unique:diplomats,kra_pin_number,' . $diplomatId,
            'nhif_number' => 'nullable|string|max:50|unique:diplomats,nhif_number,' . $diplomatId,
            'nssf_number' => 'nullable|string|max:50|unique:diplomats,nssf_number,' . $diplomatId,
            'shif_number' => 'nullable|string|max:50|unique:diplomats,shif_number,' . $diplomatId,
            'sha_number' => 'nullable|string|max:50|unique:diplomats,sha_number,' . $diplomatId,
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:diplomats,bank_account_number,' . $diplomatId,
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:diplomats,mobile_money_account_number,' . $diplomatId,
            'mobile_money_account_name' => 'nullable|string|max:255',
            'entry_point' => 'nullable|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after:arrival_date',
            'diplomatic_mission' => 'nullable|string|max:255',
            'diplomatic_role' => 'nullable|integer|in:' . implode(',', [
                self::DIPLOMATIC_STATUS_UNKNOWN,
                self::DIPLOMATIC_STATUS_AMBASSADOR,
                self::DIPLOMATIC_STATUS_CONSUL,
                self::DIPLOMATIC_STATUS_ATTACHE
            ]),
            'diplomatic_rank' => 'nullable|integer|in:' . implode(',', [
                self::DIPLOMATIC_RANK_UNKNOWN,
                self::DIPLOMATIC_RANK_FIRST_SECRETARY,
                self::DIPLOMATIC_RANK_SECOND_SECRETARY
            ]),
            'diplomatic_specialization' => 'nullable|integer|in:' . implode(',', [
                self::DIPLOMATIC_SPECIALIZATION_UNKNOWN,
                self::DIPLOMATIC_SPECIALIZATION_ECONOMICS,
                self::DIPLOMATIC_SPECIALIZATION_POLITICS
            ]),
            'diplomatic_language' => 'nullable|string|max:100',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'length_of_stay' => 'nullable|integer|in:' . implode(',', array_keys(self::lengthOfStayLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
        ];
    }
    
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
    
    public function subCounty()
    {
        return $this->belongsTo(SubCounty::class, 'sub_county_id');
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
    
    public function pollingStation()
    {
        return $this->belongsTo(PollingStation::class);
    }
    
    public function consulate()
    {
        return $this->belongsTo(Consulate::class);
    }
    
    public function refugeeCenter()
    {
        return $this->belongsTo(RefugeeCenter::class);
    }
    
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    
    public function getStatusLabelAttribute()
    {
        return self::statusLabels()[$this->_status] ?? 'Unknown';
    }
    
    public function getDiplomaticRoleLabelAttribute()
    {
        return self::diplomaticStatusLabels()[$this->diplomatic_role] ?? 'Unknown';
    }
    
    public function getDiplomaticRankLabelAttribute()
    {
        return self::diplomaticRankLabels()[$this->diplomatic_rank] ?? 'Unknown';
    }
    
    public function getDiplomaticSpecializationLabelAttribute()
    {
        return self::diplomaticSpecializationLabels()[$this->diplomatic_specialization] ?? 'Unknown';
    }
    
    public function getEntryPointLabelAttribute()
    {
        return self::entryPointLabels()[$this->entry_point] ?? 'Unknown';
    }
    
    public function getLengthOfStayLabelAttribute()
    {
        return self::lengthOfStayLabels()[$this->length_of_stay] ?? 'Unknown';
    }
}
