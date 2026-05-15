<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubCounty extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'county_id',
        'name',
        'code',
        'slug',
        'description',
        'configuration'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\SubCountyRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\SubCountyResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('sub_counties', 'uuid')],
            'county_id' => 'required|integer|exists:counties,id',
            'name' => 'required|string',
            'code' => ['nullable', 'string', Rule::unique('sub_counties', 'code')],
            'slug' => ['nullable', 'string', Rule::unique('sub_counties', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('sub_counties', 'uuid')->ignore($id)],
            'county_id' => 'nullable|integer|exists:counties,id',
            'name' => 'nullable|string',
            'code' => ['nullable', 'string', Rule::unique('sub_counties', 'code')->ignore($id)],
            'slug' => ['nullable', 'string', Rule::unique('sub_counties', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class, 'county_id', 'id');
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

    
        
}
