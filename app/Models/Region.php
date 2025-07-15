<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Region extends Model
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
        'name',
        'slug',
        'description',
        'configuration'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\RegionRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\RegionResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('regions', 'uuid')],
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('regions', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('regions', 'uuid')->ignore($id)],
            'country_id' => 'nullable|exists:countries,id',
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('regions', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function counties(): BelongsToMany
    {
        return $this->belongsToMany(County::class)->withTimestamps();
    }

    public function sub_counties(): BelongsToMany
    {
        return $this->belongsToMany(SubCounty::class)->withTimestamps();
    }

    public function constituencies(): BelongsToMany
    {
        return $this->belongsToMany(Constituency::class)->withTimestamps();
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

