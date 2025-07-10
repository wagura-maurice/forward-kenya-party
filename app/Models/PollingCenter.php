<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PollingCenter extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    public const PENDING = 0;
    public const ACTIVE = 1;
    public const INACTIVE = 2;
    public const SUSPENDED = 3;

    public static function statusLabels()
    {
        return [
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        ];
    }

    public static function getStatusValueByLabel(string $label)
    {
        $statusOptions = self::getStatusOptions();
        $lowerLabel = strtolower($label);

        foreach ($statusOptions as $key => $value) {
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
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'country_id',
        'region_id',
        'county_id',
        'sub_county_id',
        'constituency_id',
        'ward_id',
        'location_id',
        'village_id',
        'name',
        'slug',
        'iso_code',
        'svg_code',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'phone_number',
        'toll_free_number',
        'fax_number',
        'email',
        'website',
        'logo',
        'banner',
        'customer_service_email',
        'customer_service_phone',
        'founded_date',
        'is_non_government_operated',
        'is_government_operated',
        'parent_center',
        'head_name',
        'number_of_employees',
        'number_of_branches',
        'total_assets',
        'currency',
        'documents',
        'description',
        'notes',
        'services_offered',
        'operating_hours',
        'social_media_links',
        'contact_person_name',
        'contact_person_telephone',
        'contact_person_email',
        'configuration',
        'is_active',
        'is_featured',
        '_status',
        'last_verified_at',
        'verified_by'
    ];

    protected $casts = [
        'founded_date' => 'date',
        'last_verified_at' => 'datetime',
        'is_non_government_operated' => 'boolean',
        'is_government_operated' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'number_of_employees' => 'integer',
        'number_of_branches' => 'integer',
        'total_assets' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'documents' => 'array',
        'social_media_links' => 'array',
        'configuration' => 'array',
        '_status' => 'integer',
    ];

    protected $dates = [
        'founded_date',
        'last_verified_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\PollingCenterRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\PollingCenterResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('polling_centers', 'uuid')],
            'type_id' => 'required|exists:polling_center_types,id',
            'category_id' => 'required|exists:polling_center_categories,id',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'required|integer|exists:regions,id',
            'county_id' => 'required|integer|exists:counties,id',
            'sub_county_id' => 'nullable|integer|exists:sub_counties,id',
            'constituency_id' => 'required|integer|exists:constituencies,id',
            'ward_id' => 'required|integer|exists:wards,id',
            'location_id' => 'required|integer|exists:locations,id',
            'village_id' => 'required|integer|exists:villages,id',
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('polling_centers', 'slug')],
            'iso_code' => ['nullable', 'string', 'size:2', Rule::unique('polling_centers', 'iso_code')],
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone_number' => 'nullable|string|max:30',
            'toll_free_number' => 'nullable|string|max:30',
            'fax_number' => 'nullable|string|max:30',
            'customer_service_email' => 'nullable|email|max:100',
            'customer_service_phone' => 'nullable|string|max:30',
            'founded_date' => 'nullable|date',
            'is_non_government_operated' => 'boolean',
            'is_government_operated' => 'boolean',
            'number_of_employees' => 'nullable|integer|min:0',
            'number_of_branches' => 'nullable|integer|min:0',
            'total_assets' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'documents' => 'nullable|array',
            'social_media_links' => 'nullable|array',
            'configuration' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            '_status' => 'integer|in:0,1,2,3',
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('polling_centers', 'uuid')->ignore($id)],
            'type_id' => 'nullable|exists:polling_center_types,id',
            'category_id' => 'nullable|exists:polling_center_categories,id',
            'country_id' => 'nullable|exists:countries,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'county_id' => 'nullable|integer|exists:counties,id',
            'sub_county_id' => 'nullable|integer|exists:sub_counties,id',
            'constituency_id' => 'nullable|integer|exists:constituencies,id',
            'ward_id' => 'nullable|integer|exists:wards,id',
            'location_id' => 'nullable|integer|exists:locations,id',
            'village_id' => 'nullable|integer|exists:villages,id',
            'name' => 'nullable|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('polling_centers', 'slug')->ignore($id)],
            'iso_code' => ['nullable', 'string', 'size:2', Rule::unique('polling_centers', 'iso_code')->ignore($id)],
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone_number' => 'nullable|string|max:30',
            'toll_free_number' => 'nullable|string|max:30',
            'fax_number' => 'nullable|string|max:30',
            'customer_service_email' => 'nullable|email|max:100',
            'customer_service_phone' => 'nullable|string|max:30',
            'founded_date' => 'nullable|date',
            'is_non_government_operated' => 'boolean',
            'is_government_operated' => 'boolean',
            'number_of_employees' => 'nullable|integer|min:0',
            'number_of_branches' => 'nullable|integer|min:0',
            'total_assets' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'documents' => 'nullable|array',
            'social_media_links' => 'nullable|array',
            'configuration' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            '_status' => 'nullable|integer|in:0,1,2,3',
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id'
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PollingCenterType::class, 'type_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PollingCenterCategory::class, 'category_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class, 'county_id', 'id');
    }

    public function sub_county(): BelongsTo
    {
        return $this->belongsTo(SubCounty::class, 'sub_county_id', 'id');
    }   
    
    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class, 'constituency_id', 'id');
    }   

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    public function consulates(): BelongsToMany
    {
        return $this->belongsToMany(Consulate::class)->withTimestamps();
    }

    public function refugee_centers(): BelongsToMany
    {
        return $this->belongsToMany(RefugeeCenter::class)->withTimestamps();
    }

    public function polling_stations(): BelongsToMany
    {
        return $this->belongsToMany(PollingStation::class)->withTimestamps();
    }

    public function polling_streams(): BelongsToMany
    {
        return $this->belongsToMany(PollingStream::class)->withTimestamps();
    }
    
}
