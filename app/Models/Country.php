<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'iso_code',
        'description',
        'configuration'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\CountryRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\CountryResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('counties', 'uuid')],
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('counties', 'slug')],
            'iso_code' => ['nullable', 'string', Rule::unique('counties', 'iso_code')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('counties', 'uuid')->ignore($id)],
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('counties', 'slug')->ignore($id)],
            'iso_code' => ['nullable', 'string', Rule::unique('counties', 'iso_code')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function counties(): HasMany
    {
        return $this->hasMany(County::class);
    }

    public function sub_counties(): HasMany
    {
        return $this->hasMany(SubCounty::class);
    }

    public function constituencies(): HasMany
    {
        return $this->hasMany(Constituency::class);
    }
    
    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

    public function consulates(): HasMany
    {
        return $this->hasMany(Consulate::class);
    }

    public function refugee_centers(): HasMany
    {
        return $this->hasMany(RefugeeCenter::class);
    }
}
