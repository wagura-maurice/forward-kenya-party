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
