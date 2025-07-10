<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Currency
 *
 * @property int $id
 * @property string $uuid
 * @property int $type_id
 * @property int $category_id
 * @property string $name
 * @property string $code
 * @property string|null $symbol
 * @property string|null $symbol_native
 * @property int $decimal_digits
 * @property int $rounding
 * @property string|null $name_plural
 * @property string|null $country_code
 * @property string|null $flag_emoji
 * @property float $exchange_rate
 * @property bool $is_base_currency
 * @property bool $is_active
 * @property int $sort_order
 * @property array|null $metadata
 * @property string|null $description
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CurrencyCategory $category
 * @property-read string $formatted_exchange_rate
 * @property-read string $formatted_symbol
 * @property-read string $status_label
 * @property-read \App\Models\CurrencyType $type
 * @method static \Database\Eloquent\Builder|Currency active()
 * @method static \Database\Eloquent\Builder|Currency baseCurrency()
 * @method static \Database\Eloquent\Builder|Currency code(string $code)
 * @method static \Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Query\Builder|Currency onlyTrashed()
 * @method static \Database\Eloquent\Builder|Currency query()
 * @method static \Database\Eloquent\Builder|Currency whereCode($value)
 * @method static \Database\Eloquent\Builder|Currency whereCountryCode($value)
 * @method static \Database\Eloquent\Builder|Currency whereCreatedAt($value)
 * @method static \Database\Eloquent\Builder|Currency whereDecimalDigits($value)
 * @method static \Database\Eloquent\Builder|Currency whereDeletedAt($value)
 * @method static \Database\Eloquent\Builder|Currency whereDescription($value)
 * @method static \Database\Eloquent\Builder|Currency whereExchangeRate($value)
 * @method static \Database\Eloquent\Builder|Currency whereFlagEmoji($value)
 * @method static \Database\Eloquent\Builder|Currency whereId($value)
 * @method static \Database\Eloquent\Builder|Currency whereIsActive($value)
 * @method static \Database\Eloquent\Builder|Currency whereIsBaseCurrency($value)
 * @method static \Database\Eloquent\Builder|Currency whereMetadata($value)
 * @method static \Database\Eloquent\Builder|Currency whereName($value)
 * @method static \Database\Eloquent\Builder|Currency whereNamePlural($value)
 * @method static \Database\Eloquent\Builder|Currency whereRounding($value)
 * @method static \Database\Eloquent\Builder|Currency whereSortOrder($value)
 * @method static \Database\Eloquent\Builder|Currency whereStatus($value)
 * @method static \Database\Eloquent\Builder|Currency whereSymbol($value)
 * @method static \Database\Eloquent\Builder|Currency whereSymbolNative($value)
 * @method static \Database\Eloquent\Builder|Currency whereTypeId($value)
 * @method static \Database\Eloquent\Builder|Currency whereUpdatedAt($value)
 * @method static \Database\Eloquent\Builder|Currency whereUuid($value)
 * @method static \Database\Eloquent\Builder|Currency withTrashed()
 * @method static \Database\Eloquent\Builder|Currency withoutTrashed()
 * @mixin \Eloquent
 */
class Currency extends Model
{
    use HasFactory, SoftDeletes;
    
    // Status constants
    public const PENDING = 0;
    public const ACTIVE = 1;
    public const INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'name',
        'code',
        'symbol',
        'symbol_native',
        'decimal_digits',
        'rounding',
        'name_plural',
        'country_code',
        'flag_emoji',
        'exchange_rate',
        'is_base_currency',
        'is_active',
        'sort_order',
        'metadata',
        'description',
        '_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'decimal_digits' => 'integer',
        'rounding' => 'integer',
        'exchange_rate' => 'decimal:8',
        'is_base_currency' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
        '_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->code)) {
                $model->code = strtoupper(Str::limit($model->name, 3, ''));
            }
        });

        static::saving(function ($model) {
            if ($model->isDirty('is_base_currency') && $model->is_base_currency) {
                // Ensure only one base currency exists
                static::where('id', '!=', $model->id)
                    ->where('is_base_currency', true)
                    ->update(['is_base_currency' => false]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function type(): BelongsTo
    {
        return $this->belongsTo(CurrencyType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CurrencyCategory::class);
    }

    public function exchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBaseCurrency($query)
    {
        return $query->where('is_base_currency', true);
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function getFormattedSymbolAttribute(): string
    {
        return $this->symbol ?? $this->code;
    }

    public function getFormattedExchangeRateAttribute(): string
    {
        return number_format($this->exchange_rate, $this->decimal_digits);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->_status] ?? 'Unknown';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => __('Pending'),
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
        ];
    }

    public static function getStatusValueByLabel(string $label): ?int
    {
        $statusOptions = array_map('strtolower', self::getStatusOptions());
        $lowerLabel = strtolower($label);
        
        return array_search($lowerLabel, $statusOptions) !== false 
            ? array_search($lowerLabel, $statusOptions)
            : null;
    }

    public static function getBaseCurrency(): ?self
    {
        return Cache::remember('base_currency', now()->addDay(), function () {
            return static::baseCurrency()->first();
        });
    }

    public static function convertAmount(float $amount, string $fromCurrency, string $toCurrency, ?string $date = null): ?float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $cacheKey = "currency_rate_{$fromCurrency}_{$toCurrency}" . ($date ? "_" . str_replace('-', '', $date) : '');
        
        return Cache::remember($cacheKey, now()->addHour(), function () use ($amount, $fromCurrency, $toCurrency, $date) {
            $from = self::where('code', $fromCurrency)->first();
            $to = self::where('code', $toCurrency)->first();

            if (!$from || !$to) {
                return null;
            }

            if ($from->is_base_currency) {
                return $amount * $to->exchange_rate;
            }

            if ($to->is_base_currency) {
                return $amount / $from->exchange_rate;
            }

            // Convert from source to base, then to target
            $baseAmount = $amount / $from->exchange_rate;
            return $baseAmount * $to->exchange_rate;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public static function createRules(): array
    {
        return [
            'type_id' => 'required|exists:currency_types,id',
            'category_id' => 'required|exists:currency_categories,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|size:3|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
            'symbol_native' => 'nullable|string|max:10',
            'decimal_digits' => 'sometimes|integer|min:0|max:8',
            'rounding' => 'sometimes|integer|min:0',
            'name_plural' => 'nullable|string|max:100',
            'country_code' => 'nullable|string|size:2',
            'flag_emoji' => 'nullable|string|max:16',
            'exchange_rate' => 'required|numeric|min:0',
            'is_base_currency' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
            'metadata' => 'nullable|array',
            'description' => 'nullable|string',
            '_status' => 'sometimes|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    public static function updateRules(int $id): array
    {
        return [
            'type_id' => 'sometimes|exists:currency_types,id',
            'category_id' => 'sometimes|exists:currency_categories,id',
            'name' => 'sometimes|string|max:100',
            'code' => 'sometimes|string|size:3|unique:currencies,code,' . $id,
            'symbol' => 'nullable|string|max:10',
            'symbol_native' => 'nullable|string|max:10',
            'decimal_digits' => 'sometimes|integer|min:0|max:8',
            'rounding' => 'sometimes|integer|min:0',
            'name_plural' => 'nullable|string|max:100',
            'country_code' => 'nullable|string|size:2',
            'flag_emoji' => 'nullable|string|max:16',
            'exchange_rate' => 'sometimes|numeric|min:0',
            'is_base_currency' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
            'metadata' => 'nullable|array',
            'description' => 'nullable|string',
            '_status' => 'sometimes|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function makeBaseCurrency(): bool
    {
        if ($this->is_base_currency) {
            return true;
        }

        return DB::transaction(function () {
            // Remove base status from current base currency
            static::where('is_base_currency', true)
                ->update(['is_base_currency' => false]);

            // Set this currency as base
            $this->is_base_currency = true;
            $this->exchange_rate = 1;
            
            return $this->save();
        });
    }

    public function updateExchangeRate(float $rate, string $baseCurrencyCode = 'USD'): bool
    {
        if ($this->is_base_currency) {
            return false;
        }

        if (strtoupper($this->code) === strtoupper($baseCurrencyCode)) {
            return false;
        }

        $this->exchange_rate = $rate;
        return $this->save();
    }

    public function isActive(): bool
    {
        return $this->is_active && $this->_status === self::ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->_status === self::PENDING;
    }

    public function isInactive(): bool
    {
        return $this->_status === self::INACTIVE;
    }

    public function activate(): bool
    {
        $this->_status = self::ACTIVE;
        $this->is_active = true;
        return $this->save();
    }

    public function deactivate(): bool
    {
        if ($this->is_base_currency) {
            return false;
        }
        
        $this->_status = self::INACTIVE;
        $this->is_active = false;
        return $this->save();
    }
}
