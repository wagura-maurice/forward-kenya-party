<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const CONTACTED = 1;
    const DEMO_COMPLETED = 2;
    const TRAINED = 3;
    const COMPLETED = 4;    

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_visited_at' => 'datetime',
        'last_demoed_at' => 'datetime',
        'last_trained_at' => 'datetime',
        'last_contacted_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'metadata' => 'array',
        '_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
        'company_name',
        'company_email',
        'company_telephone',
        'company_address',
        'company_website',
        'description',
        'contact_person_name',
        'contact_person_telephone',
        'contact_person_email',
        'notes',
        'last_visited_at',
        'last_demoed_at',
        'last_trained_at',
        'last_contacted_at',
        'last_updated_at',
        'metadata',
        '_status',
    ];

    /**
     * Get status labels with their human-readable names
     * 
     * @return array
     */
    public static function statusLabels(): array
    {
        return [
            self::PENDING => 'Pending',
            self::CONTACTED => 'Contacted',
            self::DEMO_COMPLETED => 'Demo Completed',
            self::TRAINED => 'Trained',
            self::COMPLETED => 'Completed',
        ];
    }

    /**
     * Get status value by its label
     * 
     * @param string $label
     * @return int|false
     */
    public static function getStatusValueByLabel(string $label)
    {
        return self::getValueByLabel(self::statusLabels(), $label);
    }
    
    /**
     * Helper method to get value by label from an array of options
     * 
     * @param array $options
     * @param string $label
     * @return mixed
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
     * Get the validation rules for creating a new guest.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => 'required|uuid|unique:guests,uuid',
            'user_id' => 'required|exists:users,id',
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
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_telephone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_telephone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'last_visited_at' => 'nullable|date',
            'last_demoed_at' => 'nullable|date',
            'last_trained_at' => 'nullable|date',
            'last_contacted_at' => 'nullable|date',
            'last_updated_at' => 'nullable|date',
            'metadata' => 'nullable|array',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }

    /**
     * Get the validation rules for updating an existing guest.
     *
     * @param int $guestId
     * @return array
     */
    public static function updateRules($guestId): array
    {
        return [
            'uuid' => 'nullable|uuid|unique:guests,uuid,' . $guestId,
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
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_telephone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_telephone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'last_visited_at' => 'nullable|date',
            'last_demoed_at' => 'nullable|date',
            'last_trained_at' => 'nullable|date',
            'last_contacted_at' => 'nullable|date',
            'last_updated_at' => 'nullable|date',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }

    /**
     * Get the user that owns the guest.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country of the guest.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the region of the guest.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the county of the guest.
     */
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    /**
     * Get the sub-county of the guest.
     */
    public function subCounty()
    {
        return $this->belongsTo(SubCounty::class);
    }

    /**
     * Get the constituency of the guest.
     */
    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }

    /**
     * Get the ward of the guest.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the location of the guest.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the village of the guest.
     */
    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Get the polling station of the guest.
     */
    public function pollingStation()
    {
        return $this->belongsTo(PollingStation::class);
    }

    /**
     * Get the consulate of the guest.
     */
    public function consulate()
    {
        return $this->belongsTo(Consulate::class);
    }

    /**
     * Get the refugee center of the guest.
     */
    public function refugeeCenter()
    {
        return $this->belongsTo(RefugeeCenter::class);
    }

    /**
     * Get the status label attribute.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->_status] ?? 'Unknown';
    }

    /**
     * Scope a query to only include pending guests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('_status', self::PENDING);
    }

    /**
     * Scope a query to only include contacted guests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContacted(Builder $query): Builder
    {
        return $query->where('_status', self::CONTACTED);
    }

    /**
     * Scope a query to only include guests who completed demo.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDemoCompleted(Builder $query): Builder
    {
        return $query->where('_status', self::DEMO_COMPLETED);
    }

    /**
     * Scope a query to only include trained guests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTrained(Builder $query): Builder
    {
        return $query->where('_status', self::TRAINED);
    }

    /**
     * Scope a query to only include completed guests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('_status', self::COMPLETED);
    }

    /**
     * Get the request class for the model.
     *
     * @return string
     */
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\GuestRequest::class;
    }

    /**
     * Get the resource class for the model.
     *
     * @return string
     */
    /**
     * Get the resource class for the model.
     *
     * @return string
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\GuestResource::class;
    }
}
