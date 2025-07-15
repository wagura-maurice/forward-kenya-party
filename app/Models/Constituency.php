<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Constituency extends Model
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
        'county_id',
        'sub_county_id',
        'name',
        'slug',
        'description',
        'configuration'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\ConstituencyRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ConstituencyResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('constituencies', 'uuid')],
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'required|integer|exists:regions,id',
            'county_id' => 'required|integer|exists:counties,id',
            'sub_county_id' => 'nullable|integer|exists:sub_counties,id',
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('constituencies', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('constituencies', 'uuid')->ignore($id)],
            'country_id' => 'nullable|exists:countries,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'county_id' => 'nullable|integer|exists:counties,id',
            'sub_county_id' => 'nullable|integer|exists:sub_counties,id',
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('constituencies', 'slug')->ignore($id)],
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

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class, 'county_id', 'id');
    }

    public function sub_county(): BelongsTo
    {
        return $this->belongsTo(SubCounty::class, 'sub_county_id', 'id');
    }   
    
    public function wards(): BelongsToMany
    {
        return $this->belongsToMany(Ward::class)->withTimestamps();
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class)->withTimestamps();
    }

    public function villages(): BelongsToMany
    {
        return $this->belongsToMany(Village::class)->withTimestamps();
    }

    public function polling_stations(): BelongsToMany
    {
        return $this->belongsToMany(PollingStation::class)->withTimestamps();
    }

    public function consulates(): BelongsToMany
    {
        return $this->belongsToMany(Consulate::class)->withTimestamps();
    }

    public function refugee_centers(): BelongsToMany
    {
        return $this->belongsToMany(RefugeeCenter::class)->withTimestamps();
    }
    
}
