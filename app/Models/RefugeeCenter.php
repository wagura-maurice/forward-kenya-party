<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefugeeCenter extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
    const SUSPENDED = 3;
    
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
        $statusOptions = self::statusLabels();
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
        'county_id',
        'region_id',
        'sub_county_id',
        'constituency_id',
        'ward_id',
        'location_id',
        'village_id',
        'name',
        'slug',
        'refugee_center_email',
        'refugee_center_telephone',
        'refugee_center_address',
        'refugee_center_website',
        'description',
        'contact_person_name',
        'contact_person_telephone',
        'contact_person_email',
        'notes',
        'configuration',
        '_status',
    ];
    
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\RefugeeCenterRequest::class;
    }
    
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\RefugeeCenterResource::class;
    }
    
    public static function createRules()
    {
        return [
            'uuid' => 'required|uuid|unique:refugee_centers,uuid',
            'county_id' => 'required|exists:counties,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'sub_county_id' => 'nullable|integer|exists:sub_counties,id',
            'constituency_id' => 'nullable|integer|exists:constituencies,id',
            'ward_id' => 'nullable|integer|exists:wards,id',
            'location_id' => 'nullable|integer|exists:locations,id',
            'village_id' => 'nullable|integer|exists:villages,id',
            'name' => 'required|string',
            'slug' => 'nullable|string|unique:refugee_centers,slug',
            'refugee_center_email' => 'nullable|email',
            'refugee_center_telephone' => 'nullable|string',
            'refugee_center_address' => 'nullable|string',
            'refugee_center_website' => 'nullable|url',
            'description' => 'nullable|string',
            'contact_person_name' => 'nullable|string',
            'contact_person_telephone' => 'nullable|string',
            'contact_person_email' => 'nullable|email',
            'notes' => 'nullable|string',
            'configuration' => 'nullable|json',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }
    
    public static function updateRules()
    {
        return [
            'uuid' => 'nullable|uuid|unique:refugee_centers,uuid',
            'county_id' => 'nullable|exists:counties,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'sub_county_id' => 'nullable|exists:sub_counties,id',
            'constituency_id' => 'nullable|exists:constituencies,id',
            'ward_id' => 'nullable|exists:wards,id',
            'name' => 'nullable|string',
            'slug' => 'nullable|string|unique:refugee_centers,slug',
            'refugee_center_email' => 'nullable|email',
            'refugee_center_telephone' => 'nullable|string',
            'refugee_center_address' => 'nullable|string',
            'refugee_center_website' => 'nullable|url',
            'description' => 'nullable|string',
            'contact_person_name' => 'nullable|string',
            'contact_person_telephone' => 'nullable|string',
            'contact_person_email' => 'nullable|email',
            'notes' => 'nullable|string',
            'configuration' => 'nullable|json',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
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

}
