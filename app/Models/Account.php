<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB as DatabaseFacade;
use Illuminate\Support\Str;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_CLOSED = 3;
    
    // Account type constants
    const TYPE_ASSET = 'asset';
    const TYPE_LIABILITY = 'liability';
    const TYPE_EQUITY = 'equity';
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    // Common account subtypes
    const SUBTYPE_CASH = 'cash';
    const SUBTYPE_BANK = 'bank';
    const SUBTYPE_RECEIVABLE = 'receivable';
    const SUBTYPE_PAYABLE = 'payable';
    const SUBTYPE_TAX = 'tax';
    const SUBTYPE_SALARY = 'salary';
    const SUBTYPE_UTILITY = 'utility';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'number',
        'type',
        'subtype',
        'parent_id',
        'opening_balance',
        'current_balance',
        'currency',
        'credit_limit',
        'interest_rate',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_swift_code',
        'bank_iban',
        'is_active',
        'is_system_account',
        'is_cash_account',
        'is_tax_account',
        'tax_code',
        'description',
        'metadata',
        'created_by',
        'updated_by',
        'last_reconciled_at',
        'opened_at',
        'closed_at',
        '_status',
        'configuration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'interest_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'is_system_account' => 'boolean',
        'is_cash_account' => 'boolean',
        'is_tax_account' => 'boolean',
        'metadata' => 'array',
        'configuration' => 'array',
        'last_reconciled_at' => 'datetime',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'type_label',
        'formatted_balance',
        'is_overdrawn',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($account) {
            if (empty($account->uuid)) {
                $account->uuid = (string) Str::uuid();
            }
            
            if (empty($account->number)) {
                $account->number = static::generateAccountNumber();
            }
            
            if (empty($account->opened_at)) {
                $account->opened_at = now();
            }
            
            if (empty($account->current_balance)) {
                $account->current_balance = $account->opening_balance ?? 0;
            }
            
            if (empty($account->currency)) {
                $account->currency = 'KES'; // Default currency
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the parent account if this is a sub-account.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get all child accounts.
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get the user who created this account.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this account.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all transactions for this account.
     */
    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the status label for the account.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->_status] ?? 'Unknown';
    }

    /**
     * Get the type label for the account.
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Get the formatted balance with currency symbol.
     */
    public function getFormattedBalanceAttribute(): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'KES' => 'KSh',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;
        return $symbol . ' ' . number_format($this->current_balance, 2);
    }

    /**
     * Check if the account is overdrawn.
     */
    public function getIsOverdrawnAttribute(): bool
    {
        return $this->current_balance < 0 && in_array($this->type, [self::TYPE_ASSET, self::TYPE_EXPENSE]);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include active accounts.
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include accounts of a specific type.
     */
    public function scopeOfType(\Illuminate\Database\Eloquent\Builder $query, string $type): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include cash accounts.
     */
    public function scopeCashAccounts(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_cash_account', true);
    }

    /**
     * Scope a query to only include bank accounts.
     */
    public function scopeBankAccounts(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('subtype', self::SUBTYPE_BANK);
    }

    /**
     * Scope a query to only include tax accounts.
     */
    public function scopeTaxAccounts(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_tax_account', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Generate a unique account number.
     */
    public static function generateAccountNumber(): string
    {
        $prefix = 'ACC' . date('Ymd');
        $lastAccount = static::where('number', 'like', "{$prefix}%")
                           ->orderBy('id', 'desc')
                           ->first();

        if ($lastAccount) {
            $lastNumber = (int) substr($lastAccount->number, -6);
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '000001';
        }

        return "{$prefix}{$nextNumber}";
    }

    /**
     * Update the account balance.
     *
     * @param  float  $amount
     * @param  string  $type  'debit' or 'credit'
     * @return bool
     */
    public function updateBalance(float $amount, string $type = 'debit'): bool
    {
        if (!in_array($type, ['debit', 'credit'])) {
            throw new \InvalidArgumentException("Invalid transaction type. Must be 'debit' or 'credit'.");
        }

        return DatabaseFacade::transaction(function () use ($amount, $type) {
            $this->refresh();
            
            if ($type === 'debit') {
                $this->current_balance += $amount;
            } else {
                $this->current_balance -= $amount;
            }
            
            return $this->save();
        });
    }

    /**
     * Reconcile the account balance with the sum of all transactions.
     *
     * @return array  Array containing reconciliation details
     */
    public function reconcile(): array
    {
        $calculatedBalance = $this->opening_balance;
        
        // Calculate balance from transactions
        $transactions = $this->transactions()
            ->where('is_reconciled', false)
            ->orderBy('transaction_date')
            ->get();
        
        foreach ($transactions as $transaction) {
            if ($transaction->debit_account_id === $this->id) {
                $calculatedBalance += $transaction->amount;
            } else {
                $calculatedBalance -= $transaction->amount;
            }
            
            // Mark as reconciled
            $transaction->update(['is_reconciled' => true]);
        }
        
        $discrepancy = $this->current_balance - $calculatedBalance;
        $this->current_balance = $calculatedBalance;
        $this->last_reconciled_at = now();
        $this->save();
        
        return [
            'previous_balance' => $this->current_balance,
            'calculated_balance' => $calculatedBalance,
            'discrepancy' => $discrepancy,
            'transactions_processed' => $transactions->count(),
            'reconciled_at' => $this->last_reconciled_at,
        ];
    }

    /**
     * Close the account.
     *
     * @param  string  $reason
     * @return bool
     */
    public function close(string $reason = ''): bool
    {
        if ($this->current_balance != 0) {
            throw new \RuntimeException('Cannot close account with non-zero balance');
        }
        
        $this->_status = self::STATUS_CLOSED;
        $this->closed_at = now();
        $this->is_active = false;
        
        if (!empty($reason)) {
            $metadata = $this->metadata ?? [];
            $metadata['closure_reason'] = $reason;
            $this->metadata = $metadata;
        }
        
        return $this->save();
    }
}
