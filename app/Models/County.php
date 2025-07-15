<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class County extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'country_id',
        'region_id',
        'name',
        'slug',
        'description',
        'configuration'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\CountyRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\CountyResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('counties', 'uuid')],
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('counties', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('counties', 'uuid')->ignore($id)],
            'country_id' => 'nullable|exists:countries,id',
            'region_id' => 'nullable|exists:regions,id',
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('counties', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function sub_counties()
    {
        return $this->hasMany(SubCounty::class, 'county_id');
    }

    public function constituencies()
    {
        return $this->hasMany(Constituency::class, 'county_id');
    }
    
    public function wards()
    {
        return $this->hasMany(Ward::class, 'county_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'county_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'county_id');
    }

    public function polling_stations()
    {
        return $this->hasMany(PollingStation::class, 'county_id');
    }

    public function consulates()
    {
        return $this->hasMany(Consulate::class, 'county_id');
    }

    public function refugee_centers()
    {
        return $this->hasMany(RefugeeCenter::class, 'county_id');
    }
    
}
