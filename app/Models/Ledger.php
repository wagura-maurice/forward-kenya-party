<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * App\Models\Ledger
 *
 * @property int $id
 * @property string $uuid
 * @property string|null $reference_number
 * @property int $transaction_id
 * @property int $account_id
 * @property int|null $linked_ledger_id
 * @property int|null $created_by
 * @property int|null $approved_by
 * @property int $entry_type
 * @property int $entry_category
 * @property float $amount
 * @property float $balance
 * @property string $currency
 * @property float $exchange_rate
 * @property bool $is_reconciled
 * @property bool $is_closing_entry
 * @property string $posting_date
 * @property string $value_date
 * @property string|null $description
 * @property string|null $notes
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $reconciled_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Ledger|null $linkedLedger
 * @property-read \App\Models\Transaction $transaction
 */
class Ledger extends Model
{
    use HasFactory, SoftDeletes;

    // Entry Type constants
    public const DEBIT = 0;
    public const CREDIT = 1;

    // Entry Category constants
    public const OPERATIONAL = 0;
    public const NON_OPERATIONAL = 1;
    public const ADJUSTMENT = 2;
    public const ACCRUAL = 3;
    public const REVERSAL = 4;
    
    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_RECONCILED = 'reconciled';
    
    /**
     * Get entry type options with labels
     * 
     * @return array
     */
    public static function getEntryTypeOptions(): array
    {
        return [
            self::DEBIT => 'Debit',
            self::CREDIT => 'Credit',
        ];
    }

    /**
     * Get entry category options with labels
     * 
     * @return array
     */
    public static function getEntryCategoryOptions(): array
    {
        return [
            self::OPERATIONAL => 'Operational',
            self::NON_OPERATIONAL => 'Non-Operational',
            self::ADJUSTMENT => 'Adjustment',
            self::ACCRUAL => 'Accrual',
            self::REVERSAL => 'Reversal',
        ];
    }
    
    /**
     * Get status options with labels
     * 
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_RECONCILED => 'Reconciled',
        ];
    }

    public static function getEntryTypeValueByLabel(string $label)
    {
        $statusOptions = self::getEntryTypeOptions();
        $lowerLabel = strtolower($label);

        foreach ($statusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function getEntryCategoryValueByLabel(string $label)
    {
        $statusOptions = self::getEntryCategoryOptions();
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
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'reference_number',
        'transaction_id',
        'account_id',
        'linked_ledger_id',
        'created_by',
        'approved_by',
        'entry_type',
        'entry_category',
        'amount',
        'balance',
        'currency',
        'exchange_rate',
        'is_reconciled',
        'is_closing_entry',
        'posting_date',
        'value_date',
        'description',
        'notes',
        'metadata',
        'approved_at',
        'reconciled_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'is_reconciled' => 'boolean',
        'is_closing_entry' => 'boolean',
        'posting_date' => 'date',
        'value_date' => 'datetime',
        'approved_at' => 'datetime',
        'reconciled_at' => 'datetime',
        'metadata' => 'array',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'entry_type_label',
        'entry_category_label',
        '_status',
        'formatted_amount',
    ];
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($ledger) {
            if (empty($ledger->uuid)) {
                $ledger->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            
            if (empty($ledger->reference_number)) {
                $ledger->reference_number = static::generateReferenceNumber();
            }
            
            if (empty($ledger->created_by) && Auth::check()) {
                $ledger->created_by = Auth::id();
            }
            
            if (empty($ledger->posting_date)) {
                $ledger->posting_date = now()->toDateString();
            }
            
            if (empty($ledger->value_date)) {
                $ledger->value_date = now();
            }
        });
        
        static::created(function ($ledger) {
            // Update the account balance when a new ledger entry is created
            $ledger->account->updateBalance($ledger);
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'uuid', 'max:36'],
            'reference_number' => ['nullable', 'string', 'max:100', 'unique:ledgers,reference_number'],
            'transaction_id' => ['required', 'integer', 'exists:transactions,id'],
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'linked_ledger_id' => ['nullable', 'integer', 'exists:ledgers,id'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'approved_by' => ['nullable', 'integer', 'exists:users,id'],
            'entry_type' => ['required', 'integer', 'in:' . implode(',', array_keys(self::getEntryTypeOptions()))],
            'entry_category' => ['required', 'integer', 'in:' . implode(',', array_keys(self::getEntryCategoryOptions()))],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'balance' => ['nullable', 'numeric', 'min:-999999999999.99', 'max:999999999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['required', 'numeric', 'min:0', 'max:999999.999999'],
            'is_reconciled' => ['boolean'],
            'is_closing_entry' => ['boolean'],
            'posting_date' => ['required', 'date'],
            'value_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'approved_at' => ['nullable', 'date'],
            'reconciled_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get the validation rules for updating the model.
     *
     * @param int $id
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function updateRules(int $id): array
    {
        return array_merge(self::createRules(), [
            'uuid' => ['nullable', 'string', 'uuid', 'max:36', Rule::unique('ledgers', 'uuid')->ignore($id)],
            'reference_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('ledgers', 'reference_number')->ignore($id)
            ],
            'transaction_id' => ['sometimes', 'required', 'integer', 'exists:transactions,id'],
            'account_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
        ]);
    }
    
    /**
     * Get the request class for the model.
     */
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\LedgerRequest::class;
    }

    /**
     * Get the resource class for the model.
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\LedgerResource::class;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the transaction that owns the ledger entry.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the account that owns the ledger entry.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the linked ledger entry.
     */
    public function linkedLedger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'linked_ledger_id');
    }

    /**
     * Get the user who created the ledger entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the ledger entry.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the entry type label.
     */
    public function getEntryTypeLabelAttribute(): string
    {
        return self::getEntryTypeOptions()[$this->entry_type] ?? 'Unknown';
    }
    
    /**
     * Get the entry category label.
     */
    public function getEntryCategoryLabelAttribute(): string
    {
        return self::getEntryCategoryOptions()[$this->entry_category] ?? 'Unknown';
    }
    
    /**
     * Get the status of the ledger entry.
     */
    public function getStatusAttribute(): string
    {
        if ($this->is_reconciled && $this->reconciled_at) {
            return self::STATUS_RECONCILED;
        }
        
        if ($this->approved_at) {
            return self::STATUS_APPROVED;
        }
        
        return self::STATUS_PENDING;
    }
    
    /**
     * Get the formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        $amount = number_format($this->amount, 2);
        $currency = $this->currency;
        
        // Add currency symbol based on currency code
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'KES' => 'KSh',
            // Add more currency symbols as needed
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        
        return $this->entry_type === self::DEBIT 
            ? "{$symbol} {$amount}" 
            : "-{$symbol} {$amount}";
    }
    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    
    /**
     * Scope a query to only include debit entries.
     */
    public function scopeDebits($query)
    {
        return $query->where('entry_type', self::DEBIT);
    }
    
    /**
     * Scope a query to only include credit entries.
     */
    public function scopeCredits($query)
    {
        return $query->where('entry_type', self::CREDIT);
    }
    
    /**
     * Scope a query to only include entries for a specific account.
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }
    
    /**
     * Scope a query to only include entries within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?: $startDate;
        
        return $query->whereBetween('posting_date', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ]);
    }
    
    /**
     * Scope a query to only include reconciled entries.
     */
    public function scopeReconciled($query, $reconciled = true)
    {
        return $query->where('is_reconciled', $reconciled);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */
    
    /**
     * Approve the ledger entry.
     *
     * @param int $userId
     * @return bool
     */
    public function approve(int $userId): bool
    {
        return $this->update([
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }
    
    /**
     * Mark the ledger entry as reconciled.
     *
     * @return bool
     */
    public function markAsReconciled(): bool
    {
        return $this->update([
            'is_reconciled' => true,
            'reconciled_at' => now(),
        ]);
    }
    
    /**
     * Generate a unique reference number for the ledger entry.
     *
     * @return string
     */
    public static function generateReferenceNumber(): string
    {
        $prefix = 'LED' . date('Ymd');
        $lastLedger = static::where('reference_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
        
        if ($lastLedger) {
            $lastNumber = (int) substr($lastLedger->reference_number, -6);
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '000001';
        }
        
        return "{$prefix}{$nextNumber}";
    }
    
    /**
     * Get the running balance for an account up to a specific date.
     *
     * @param int $accountId
     * @param string|null $date
     * @return float
     */
    public static function getAccountBalance(int $accountId, ?string $date = null): float
    {
        $query = static::where('account_id', $accountId);
        
        if ($date) {
            $query->where('posting_date', '<=', $date);
        }
        
        return (float) $query->sum(DB::raw('CASE WHEN entry_type = ' . self::DEBIT . ' THEN amount ELSE -amount END'));
    }
}
