<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\CausesActivity;

class Citizen extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, CausesActivity;
    
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
     * The attributes that should be logged for the citizen.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults()
            ->useLogName('citizens')
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['last_updated_at', 'updated_at', 'last_verified_at'])
            ->logExcept([
                'created_at', 
                'updated_at', 
                'deleted_at',
                'user_id',
                'profile_id',
                'ward_id',
                'constituency_id',
                'county_id',
                'registration_point',
                'registration_latitude',
                'registration_longitude',
                'registration_accuracy',
                'registration_altitude',
                'registration_photo_path',
                'registration_device_info'
            ]);
            
        // Add properties to all logged activities
        $options->properties = array_merge($options->properties ?? [], [
            'type_id' => 1,
            'category_id' => 1,
        ]);
        
        // Set description for events
        $options->setDescriptionForEvent(function(string $eventName) {
            return match($eventName) {
                'created' => 'Citizen #'.$this->uuid.' registration was created',
                'updated' => 'Citizen #'.$this->uuid.' registration was updated',
                'deleted' => 'Citizen #'.$this->uuid.' registration was deleted',
                'restored' => 'Citizen #'.$this->uuid.' registration was restored',
                'forceDeleted' => 'Citizen #'.$this->uuid.' record was permanently deleted',
                'status_updated' => 'Citizen #'.$this->uuid.' registration status was updated',
                default => "Citizen registration was {$eventName}",
            };
        });
        
        return $options;
    }

    /**
     * Get all activities for this citizen.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Get activities where this citizen was affected.
     */
    public function affectedActivities()
    {
        return Activity::where('subject_type', self::class)
            ->where('subject_id', $this->id);
    }
    
    /**
     * Log a custom activity for this citizen.
     *
     * @param string $action
     * @param string $description
     * @param array $properties
     * @param string|null $logName
     * @return \Spatie\Activitylog\Models\Activity
     */
    public function logActivity(string $action, string $description, array $properties = [], ?string $logName = null)
    {
        $causer = auth()->user() ?? $this->user ?? null;
        
        $activity = activity($logName ?? 'citizens')
            ->performedOn($this)
            ->withProperties($properties);
            
        if ($causer) {
            $activity->causedBy($causer);
        }
            
        return $activity->log($description);
    }
    
    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::created(function ($citizen) {
            // Log citizen creation with additional context
            $citizen->logActivity(
                'created',
                'Citizen registration was created',
                [
                    'status' => $citizen->status,
                    'registration_point' => $citizen->registration_point,
                    'ward_id' => $citizen->ward_id
                ]
            );
        });
        
        static::updating(function ($citizen) {
            // Log status changes
            if ($citizen->isDirty('status')) {
                $citizen->logActivity(
                    'status_updated',
                    'Citizen registration status was updated',
                    [
                        'from' => $citizen->getOriginal('status'),
                        'to' => $citizen->status,
                        'status_label' => self::statusLabels()[$citizen->status] ?? 'Unknown'
                    ]
                );
            }
            
            // Log when important fields are updated
            $importantFields = ['first_name', 'last_name', 'id_number', 'phone_number', 'email'];
            if (count(array_intersect($importantFields, array_keys($citizen->getDirty()))) > 0) {
                $citizen->logActivity(
                    'details_updated',
                    'Citizen details were updated',
                    ['changed' => array_intersect_key($citizen->getDirty(), array_flip($importantFields))]
                );
            }
        });
        
        static::deleted(function ($citizen) {
            // Log deletion with soft delete status
            $citizen->logActivity(
                $citizen->isForceDeleting() ? 'force_deleted' : 'deleted',
                'Citizen registration was ' . ($citizen->isForceDeleting() ? 'permanently deleted' : 'soft deleted')
            );
        });
        
        static::restored(function ($citizen) {
            // Log restoration
            $citizen->logActivity('restored', 'Citizen registration was restored');
        });
    }
    
    /**
     * Get all status options with their labels"
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
