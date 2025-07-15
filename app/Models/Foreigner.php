<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Foreigner extends Model
{
    use HasFactory, SoftDeletes;

    // Visa type constants
    const SINGLE_ENTRY = 0;
    const MULTIPLE_ENTRY = 1;
    const TRANSIT = 2;
    const VISA_FREE = 3;

    // Status constants
    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;
    
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

    // Purpose of visit constants
    const TOURISM = 0;
    const BUSINESS = 1;
    const STUDY = 2;
    const WORK = 3;
    const OTHER = 4;

    public static function visaTypeLabels()
    {
        return [
            self::SINGLE_ENTRY => 'Single-entry',
            self::MULTIPLE_ENTRY => 'Multiple-entry',
            self::TRANSIT => 'Transit',
        ];
    }
    
    public static function statusLabels()
    {
        return [
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
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

    public static function purposeOfVisitLabels()
    {
        return [
            self::TOURISM => 'Tourism',
            self::BUSINESS => 'Business',
            self::STUDY => 'Study',
            self::WORK => 'Work',
            self::OTHER => 'Other',
        ];
    }

    public static function getVisaTypeValueByLabel(string $label)
    {
        $visaTypeOptions = self::visaTypeLabels();
        $lowerLabel = strtolower($label);

        foreach ($visaTypeOptions as $key => $value) {
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
    
    public static function getPurposeOfVisitValueByLabel(string $label)
    {
        return self::getValueByLabel(self::purposeOfVisitLabels(), $label);
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
        'type_of_visa' => 'integer',
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
        'purpose_of_visit',
        'type_of_visa',
        'visa_number',
        'issuing_country',
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
        return \App\Http\Requests\API\ForeignerRequest::class;
    }
    
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ForeignerResource::class;
    }
    
    public static function createRules()
    {
        return [
            'uuid' => 'required|uuid|unique:foreigners,uuid',
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
            'registration_number' => 'nullable|string|max:100|unique:foreigners,registration_number',
            'passport_number' => 'nullable|string|max:100|unique:foreigners,passport_number',
            'national_identification_number' => 'nullable|string|max:100|unique:foreigners,national_identification_number',
            'driver_license_number' => 'nullable|string|max:100|unique:foreigners,driver_license_number',
            'kra_pin_number' => 'nullable|string|max:50|unique:foreigners,kra_pin_number',
            'nhif_number' => 'nullable|string|max:50|unique:foreigners,nhif_number',
            'nssf_number' => 'nullable|string|max:50|unique:foreigners,nssf_number',
            'shif_number' => 'nullable|string|max:50|unique:foreigners,shif_number',
            'sha_number' => 'nullable|string|max:50|unique:foreigners,sha_number',
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:foreigners,bank_account_number',
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:foreigners,mobile_money_account_number',
            'mobile_money_account_name' => 'nullable|string|max:255',
            'entry_point' => 'required|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'required|date',
            'departure_date' => 'nullable|date|after:arrival_date',
            'purpose_of_visit' => 'nullable|integer|in:' . implode(',', array_keys(self::purposeOfVisitLabels())),
            'type_of_visa' => 'required|integer|in:' . implode(',', [
                self::SINGLE_ENTRY,
                self::MULTIPLE_ENTRY,
                self::TRANSIT,
                self::VISA_FREE
            ]),
            'visa_number' => 'nullable|string|max:100|unique:foreigners,visa_number',
            'issuing_country' => 'nullable|string|max:100',
            'diplomatic_language' => 'nullable|string|max:100',
            'contact_person_email' => 'required|email|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'length_of_stay' => 'required|integer|in:' . implode(',', array_keys(self::lengthOfStayLabels())),
            'configuration' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
        ];
    }
    
    public static function updateRules($foreignerId)
    {
        return [
            'uuid' => 'nullable|uuid|unique:foreigners,uuid,' . $foreignerId,
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
            'registration_number' => 'nullable|string|max:100|unique:foreigners,registration_number,' . $foreignerId,
            'passport_number' => 'nullable|string|max:100|unique:foreigners,passport_number,' . $foreignerId,
            'national_identification_number' => 'nullable|string|max:100|unique:foreigners,national_identification_number,' . $foreignerId,
            'driver_license_number' => 'nullable|string|max:100|unique:foreigners,driver_license_number,' . $foreignerId,
            'kra_pin_number' => 'nullable|string|max:50|unique:foreigners,kra_pin_number,' . $foreignerId,
            'nhif_number' => 'nullable|string|max:50|unique:foreigners,nhif_number,' . $foreignerId,
            'nssf_number' => 'nullable|string|max:50|unique:foreigners,nssf_number,' . $foreignerId,
            'shif_number' => 'nullable|string|max:50|unique:foreigners,shif_number,' . $foreignerId,
            'sha_number' => 'nullable|string|max:50|unique:foreigners,sha_number,' . $foreignerId,
            'bank_code' => 'nullable|string|max:20',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50|unique:foreigners,bank_account_number,' . $foreignerId,
            'bank_account_name' => 'nullable|string|max:255',
            'mobile_money_provider_code' => 'nullable|string|max:20',
            'mobile_money_account_number' => 'nullable|string|max:50|unique:foreigners,mobile_money_account_number,' . $foreignerId,
            'mobile_money_account_name' => 'nullable|string|max:255',
            'entry_point' => 'nullable|integer|in:' . implode(',', array_keys(self::entryPointLabels())),
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after:arrival_date',
            'purpose_of_visit' => 'nullable|integer|in:' . implode(',', array_keys(self::purposeOfVisitLabels())),
            'type_of_visa' => 'nullable|integer|in:' . implode(',', [
                self::SINGLE_ENTRY,
                self::MULTIPLE_ENTRY,
                self::TRANSIT,
                self::VISA_FREE
            ]),
            'visa_number' => 'nullable|string|max:100|unique:foreigners,visa_number,' . $foreignerId,
            'issuing_country' => 'nullable|string|max:100',
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
    
    public function getVisaTypeLabelAttribute()
    {
        return self::visaTypeLabels()[$this->type_of_visa] ?? 'Unknown';
    }
    
    public function getEntryPointLabelAttribute()
    {
        return self::entryPointLabels()[$this->entry_point] ?? 'Unknown';
    }
    
    public function getLengthOfStayLabelAttribute()
    {
        return self::lengthOfStayLabels()[$this->length_of_stay] ?? 'Unknown';
    }
    
    // All relationships are defined above
}
