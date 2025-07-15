<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassAssignmentException;

/**
 * @property int $id
 * @property string $uuid
 * @property int $from_currency
 * @property int $to_currency
 * @property float $rate
 * @property Carbon $rate_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Currency $fromCurrency
 * @property-read Currency $toCurrency
 */
class ExchangeRate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'from_currency',
        'to_currency',
        'rate',
        'rate_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rate' => 'decimal:6',
        'rate_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if ($model->from_currency === $model->to_currency) {
                throw new MassAssignmentException('From currency and to currency cannot be the same.');
            }
            
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::orderedUuid();
            }
        });
    }

    /**
     * Get the request class for the model.
     */
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\ExchangeRateRequest::class;
    }

    /**
     * Get the resource class for the model.
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ExchangeRateResource::class;
    }

    /**
     * Get the validation rules for creating a new model.
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'uuid', Rule::unique('exchange_rates', 'uuid')],
            'from_currency' => [
                'required',
                'integer',
                'exists:currencies,id',
                'different:to_currency',
            ],
            'to_currency' => [
                'required',
                'integer',
                'exists:currencies,id',
                'different:from_currency',
            ],
            'rate' => 'required|numeric|min:0',
            'rate_at' => 'nullable|date',
        ];
    }

    /**
     * Get the validation rules for updating the model.
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', 'uuid', Rule::unique('exchange_rates', 'uuid')->ignore($id)],
            'from_currency' => [
                'nullable',
                'integer',
                'exists:currencies,id',
                'different:to_currency',
            ],
            'to_currency' => [
                'nullable',
                'integer',
                'exists:currencies,id',
                'different:from_currency',
            ],
            'rate' => 'nullable|numeric|min:0',
            'rate_at' => 'nullable|date',
        ];
    }

    /**
     * Get the from currency relationship.
     */
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency');
    }

    /**
     * Get the to currency relationship.
     */
    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency');
    }

    /**
     * Scope a query to only include the latest exchange rate for each currency pair.
     */
    public function scopeLatestRate(Builder $query): Builder
    {
        return $query->whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('exchange_rates')
                ->groupBy('from_currency', 'to_currency');
        });
    }

    /**
     * Scope a query to only include exchange rates for a specific date.
     */
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('rate_at', '<=', $date)
            ->whereIn('id', function ($query) use ($date) {
                $query->selectRaw('MAX(id)')
                    ->from('exchange_rates')
                    ->whereDate('rate_at', '<=', $date)
                    ->groupBy('from_currency', 'to_currency');
            });
    }

    /**
     * Get the inverse exchange rate.
     */
    public function getInverseRate(): float
    {
        return $this->rate > 0 ? 1 / $this->rate : 0;
    }

    /**
     * Convert an amount from the from_currency to the to_currency.
     */
    public function convert(float $amount): float
    {
        return $amount * $this->rate;
    }

    /**
     * Find an exchange rate between two currencies.
     */
    public static function findRate(int $fromCurrencyId, int $toCurrencyId, ?string $date = null): ?self
    {
        if ($fromCurrencyId === $toCurrencyId) {
            $rate = new self();
            $rate->rate = 1;
            return $rate;
        }

        $query = self::where('from_currency', $fromCurrencyId)
            ->where('to_currency', $toCurrencyId);

        if ($date) {
            $query->whereDate('rate_at', '<=', $date);
        }

        return $query->latest('rate_at')->first();
    }
}
