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

    // Government type constants
    const REPUBLIC = 0;
    const PRESIDENTIAL = 1;
    const PARLIAMENTARY = 2;
    const MONARCHY = 3;

    public static function getGovernmentTypeOptions(): array
    {
        return [
            self::REPUBLIC => 'Republic',
            self::PRESIDENTIAL => 'Presidential',
            self::PARLIAMENTARY => 'Parliamentary',
            self::MONARCHY => 'Monarchy',
        ];
    }

    public static function getGovernmentTypeValueByLabel(string $label)
    {
        $governmentTypeOptions = self::getGovernmentTypeOptions();
        $lowerLabel = strtolower($label);

        foreach ($governmentTypeOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    // Driving side constants
    const LEFT = 0;
    const RIGHT = 1;

    public static function getDrivingSideOptions(): array
    {
        return [
            self::LEFT => 'Left',
            self::RIGHT => 'Right'
        ];
    }

    public static function getDrivingSideValueByLabel(string $label)
    {
        $drivingSideOptions = self::getDrivingSideOptions();
        $lowerLabel = strtolower($label);

        foreach ($drivingSideOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    // Status constants
    const PENDING   = 0;
    const ACTIVE    = 1;
    const INACTIVE  = 2;
    const SUSPENDED = 3;

    public static function getStatusOptions(): array
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
        'name',
        'slug',
        'demonym',
        'description',
        'notes',
        'sort_order',
        'iso_code',
        'iso3_code',
        'numeric_code',
        'tld',
        'currency_code',
        'currency_name',
        'currency_symbol',
        'currency_rate',
        'capital_city',
        'region',
        'subregion',
        'latitude',
        'longitude',
        'area',
        'population',
        'government_type',
        'driving_side',
        'phone_code',
        'zip_code_format',
        'languages',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => 'string',
        'sort_order' => 'integer',
        'currency_rate' => 'decimal:6',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'area' => 'decimal:2',
        'population' => 'integer',
        'government_type' => 'integer',
        'driving_side' => 'integer',
        'languages' => 'array',
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
            'uuid' => ['nullable', 'string', Rule::unique('countries', 'uuid')],
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('countries', 'slug')],
            'demonym' => 'nullable|string',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'iso_code' => ['nullable', 'string', 'size:2', Rule::unique('countries', 'iso_code')],
            'iso3_code' => ['nullable', 'string', 'size:3', Rule::unique('countries', 'iso3_code')],
            'numeric_code' => ['nullable', 'string', 'size:3'],
            'tld' => 'nullable|string',
            'currency_code' => ['nullable', 'string', 'size:3'],
            'currency_name' => 'nullable|string',
            'currency_symbol' => 'nullable|string',
            'currency_rate' => 'nullable|numeric',
            'capital_city' => 'nullable|string',
            'region' => 'nullable|string',
            'subregion' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'population' => 'nullable|integer',
            'government_type' => 'nullable|integer',
            'driving_side' => 'nullable|integer',
            'phone_code' => 'nullable|string',
            'zip_code_format' => 'nullable|string',
            'languages' => 'nullable|array',
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('countries', 'uuid')->ignore($id)],
            'name' => 'sometimes|required|string',
            'slug' => ['nullable', 'string', Rule::unique('countries', 'slug')->ignore($id)],
            'demonym' => 'nullable|string',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'iso_code' => ['nullable', 'string', 'size:2', Rule::unique('countries', 'iso_code')->ignore($id)],
            'iso3_code' => ['nullable', 'string', 'size:3', Rule::unique('countries', 'iso3_code')->ignore($id)],
            'numeric_code' => ['nullable', 'string', 'size:3'],
            'tld' => 'nullable|string',
            'currency_code' => ['nullable', 'string', 'size:3'],
            'currency_name' => 'nullable|string',
            'currency_symbol' => 'nullable|string',
            'currency_rate' => 'nullable|numeric',
            'capital_city' => 'nullable|string',
            'region' => 'nullable|string',
            'subregion' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'population' => 'nullable|integer',
            'government_type' => 'nullable|integer',
            'driving_side' => 'nullable|integer',
            'phone_code' => 'nullable|string',
            'zip_code_format' => 'nullable|string',
            'languages' => 'nullable|array',
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
