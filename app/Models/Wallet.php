<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
    const SUSPENDED = 3;

    // Default precision for decimal calculations
    const DECIMAL_PRECISION = 8;
    const DECIMAL_SCALE = 2;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'available_balance' => 'decimal:'.self::DECIMAL_PRECISION,
        'pending_balance' => 'decimal:'.self::DECIMAL_PRECISION,
        'hold_balance' => 'decimal:'.self::DECIMAL_PRECISION,
        'total_credit' => 'decimal:'.self::DECIMAL_PRECISION,
        'total_debit' => 'decimal:'.self::DECIMAL_PRECISION,
        'daily_limit' => 'decimal:'.self::DECIMAL_PRECISION,
        'transaction_limit' => 'decimal:'.self::DECIMAL_PRECISION,
        'monthly_limit' => 'decimal:'.self::DECIMAL_PRECISION,
        'is_primary' => 'boolean',
        'is_locked' => 'boolean',
        'locked_until' => 'datetime',
        'initialized_at' => 'datetime',
        'last_transaction_at' => 'datetime',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'metadata' => 'array',
        'verification_data' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'available_balance' => 0,
        'pending_balance' => 0,
        'hold_balance' => 0,
        'total_credit' => 0,
        'total_debit' => 0,
        '_status' => self::PENDING,
        'is_primary' => false,
        'is_locked' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'currency_id',
        'available_balance',
        'pending_balance',
        'hold_balance',
        'total_credit',
        'total_debit',
        'name',
        'account_reference',
        'bank_name',
        'account_number',
        'account_name',
        '_status',
        'daily_limit',
        'transaction_limit',
        'monthly_limit',
        'is_primary',
        'is_locked',
        'locked_until',
        'lock_reason',
        'initialized_at',
        'metadata',
        'verification_data',
        'ip_address',
        'user_agent',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_label',
        'formatted_balance',
        'is_active',
        'is_suspended',
        'is_operational',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wallet) {
            if (empty($wallet->uuid)) {
                $wallet->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (empty($wallet->initialized_at)) {
                $wallet->initialized_at = now();
            }
        });

        static::created(function ($wallet) {
            // If this is the first wallet for the user, make it primary
            if (!self::where('user_id', $wallet->user_id)->where('id', '!=', $wallet->id)->exists()) {
                $wallet->update(['is_primary' => true]);
            }
        });
    }

    /**
     * Get the status options for wallets.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        ];
    }

    /**
     * Get the status label attribute.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->_status] ?? 'Unknown';
    }

    /**
     * Get the status value by label.
     *
     * @param string $label
     * @return int|false
     */
    public static function getStatusValueByLabel(string $label)
    {
        $statusOptions = self::getStatusOptions();
        $lowerLabel = strtolower($label);

        foreach ($statusOptions as $key => $value) {
            if (strtolower($value) === $lowerLabel) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Get the formatted balance attribute.
     *
     * @return string
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->available_balance, self::DECIMAL_SCALE, '.', ',');
    }

    /**
     * Check if wallet is active.
     *
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->_status === self::ACTIVE;
    }

    /**
     * Check if wallet is suspended.
     *
     * @return bool
     */
    public function getIsSuspendedAttribute(): bool
    {
        return $this->_status === self::SUSPENDED;
    }

    /**
     * Check if wallet is operational (active and not locked).
     *
     * @return bool
     */
    public function getIsOperationalAttribute(): bool
    {
        return $this->is_active && !$this->is_locked && 
               (!$this->locked_until || $this->locked_until->isPast());
    }

    /**
     * Get the validation rules for creating a new wallet.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'name' => ['nullable', 'string', 'max:100'],
            'account_reference' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'is_primary' => ['boolean'],
            'daily_limit' => ['nullable', 'numeric', 'min:0'],
            'transaction_limit' => ['nullable', 'numeric', 'min:0'],
            'monthly_limit' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get the validation rules for updating an existing wallet.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'is_primary' => ['boolean'],
            'daily_limit' => ['nullable', 'numeric', 'min:0'],
            'transaction_limit' => ['nullable', 'numeric', 'min:0'],
            'monthly_limit' => ['nullable', 'numeric', 'min:0'],
            '_status' => ['nullable', 'integer', Rule::in(array_keys(self::getStatusOptions()))],
        ];
    }

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the currency of the wallet.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get all transactions for the wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all pending transactions for the wallet.
     */
    public function pendingTransactions()
    {
        return $this->transactions()->where('_status', Transaction::PENDING);
    }

    /**
     * Get all completed transactions for the wallet.
     */
    public function completedTransactions()
    {
        return $this->transactions()->where('_status', Transaction::COMPLETED);
    }

    /**
     * Get all failed transactions for the wallet.
     */
    public function failedTransactions()
    {
        return $this->transactions()->where('_status', Transaction::FAILED);
    }

    /**
     * Get all credit transactions for the wallet.
     */
    public function creditTransactions()
    {
        return $this->transactions()->where('type', Transaction::CREDIT);
    }

    /**
     * Get all debit transactions for the wallet.
     */
    public function debitTransactions()
    {
        return $this->transactions()->where('type', Transaction::DEBIT);
    }

    /**
     * Check if the wallet can perform a transaction of the given amount.
     *
     * @param float $amount
     * @return bool
     */
    public function canTransact(float $amount): bool
    {
        if (!$this->is_operational) {
            return false;
        }

        // Check available balance
        if ($this->available_balance < $amount) {
            return false;
        }

        // Check transaction limit
        if ($this->transaction_limit && $amount > $this->transaction_limit) {
            return false;
        }

        // Check daily limit
        $dailyTotal = $this->debitTransactions()
            ->whereDate('created_at', now())
            ->sum('amount');
            
        if ($this->daily_limit && ($dailyTotal + $amount) > $this->daily_limit) {
            return false;
        }

        // Check monthly limit
        $monthlyTotal = $this->debitTransactions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
            
        if ($this->monthly_limit && ($monthlyTotal + $amount) > $this->monthly_limit) {
            return false;
        }

        return true;
    }

    /**
     * Credit the wallet with the given amount.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function credit(float $amount, string $description = '', array $metadata = [])
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }

        return \DB::transaction(function () use ($amount, $description, $metadata) {
            $this->available_balance += $amount;
            $this->total_credit += $amount;
            $this->last_transaction_at = now();
            
            if ($this->_status === self::PENDING) {
                $this->_status = self::ACTIVE;
                $this->activated_at = now();
            }
            
            $this->save();

            // Create transaction record
            return $this->transactions()->create([
                'amount' => $amount,
                'type' => Transaction::CREDIT,
                '_status' => Transaction::COMPLETED,
                'description' => $description,
                'metadata' => $metadata,
                'balance_after' => $this->available_balance,
            ]);
        });
    }

    /**
     * Debit the wallet with the given amount.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @param bool $hold Whether to hold the amount instead of debiting immediately
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function debit(float $amount, string $description = '', array $metadata = [], bool $hold = false)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }

        if (!$this->canTransact($amount)) {
            throw new \RuntimeException('Insufficient funds or transaction not allowed');
        }

        return \DB::transaction(function () use ($amount, $description, $metadata, $hold) {
            if ($hold) {
                $this->hold_balance += $amount;
            } else {
                $this->available_balance -= $amount;
                $this->total_debit += $amount;
            }
            
            $this->last_transaction_at = now();
            $this->save();

            // Create transaction record
            return $this->transactions()->create([
                'amount' => $amount,
                'type' => Transaction::DEBIT,
                '_status' => $hold ? Transaction::PENDING : Transaction::COMPLETED,
                'description' => $description,
                'metadata' => $metadata,
                'balance_after' => $this->available_balance - ($hold ? $amount : 0),
                'is_held' => $hold,
            ]);
        });
    }

    /**
     * Release a held amount back to available balance.
     *
     * @param float $amount
     * @param string $description
     * @return void
     * @throws \Exception
     */
    public function releaseHold(float $amount, string $description = 'Hold released')
    {
        if ($amount <= 0 || $amount > $this->hold_balance) {
            throw new \InvalidArgumentException('Invalid amount to release');
        }

        $this->hold_balance -= $amount;
        $this->available_balance += $amount;
        $this->save();

        // Update the held transaction if it exists
        $this->transactions()
            ->where('is_held', true)
            ->where('_status', Transaction::PENDING)
            ->where('amount', $amount)
            ->orderBy('created_at', 'desc')
            ->first()
            ?->update([
                'is_held' => false,
                '_status' => Transaction::COMPLETED,
                'description' => $description,
                'balance_after' => $this->available_balance,
            ]);
    }

    /**
     * Mark a held amount as completed (debit from held balance).
     *
     * @param float $amount
     * @param string $description
     * @return void
     * @throws \Exception
     */
    public function completeHold(float $amount, string $description = 'Hold completed')
    {
        if ($amount <= 0 || $amount > $this->hold_balance) {
            throw new \InvalidArgumentException('Invalid amount to complete');
        }

        $this->hold_balance -= $amount;
        $this->total_debit += $amount;
        $this->save();

        // Update the held transaction
        $this->transactions()
            ->where('is_held', true)
            ->where('_status', Transaction::PENDING)
            ->where('amount', $amount)
            ->orderBy('created_at', 'desc')
            ->first()
            ?->update([
                'is_held' => false,
                '_status' => Transaction::COMPLETED,
                'description' => $description,
                'balance_after' => $this->available_balance,
            ]);
    }

    /**
     * Transfer funds to another wallet.
     *
     * @param Wallet $recipient
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return array
     * @throws \Exception
     */
    public function transfer(Wallet $recipient, float $amount, string $description = '', array $metadata = [])
    {
        if (!$this->is_operational || !$recipient->is_operational) {
            throw new \RuntimeException('One or both wallets are not operational');
        }

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }

        if ($this->available_balance < $amount) {
            throw new \RuntimeException('Insufficient funds');
        }

        return \DB::transaction(function () use ($recipient, $amount, $description, $metadata) {
            // Debit from sender
            $this->available_balance -= $amount;
            $this->total_debit += $amount;
            $this->last_transaction_at = now();
            $this->save();

            // Credit to recipient
            $recipient->available_balance += $amount;
            $recipient->total_credit += $amount;
            $recipient->last_transaction_at = now();
            
            if ($recipient->_status === self::PENDING) {
                $recipient->_status = self::ACTIVE;
                $recipient->activated_at = now();
            }
            
            $recipient->save();

            // Create transaction records
            $debitTransaction = $this->transactions()->create([
                'amount' => $amount,
                'type' => Transaction::DEBIT,
                '_status' => Transaction::COMPLETED,
                'description' => $description ?: "Transfer to {$recipient->user->name}",
                'metadata' => array_merge($metadata, [
                    'recipient_id' => $recipient->id,
                    'recipient_user_id' => $recipient->user_id,
                ]),
                'balance_after' => $this->available_balance,
                'related_model_type' => self::class,
                'related_model_id' => $recipient->id,
            ]);

            $creditTransaction = $recipient->transactions()->create([
                'amount' => $amount,
                'type' => Transaction::CREDIT,
                '_status' => Transaction::COMPLETED,
                'description' => $description ?: "Transfer from {$this->user->name}",
                'metadata' => array_merge($metadata, [
                    'sender_id' => $this->id,
                    'sender_user_id' => $this->user_id,
                ]),
                'balance_after' => $recipient->available_balance,
                'related_model_type' => self::class,
                'related_model_id' => $this->id,
                'related_transaction_id' => $debitTransaction->id,
            ]);

            // Update the reference to the related transaction
            $debitTransaction->update(['related_transaction_id' => $creditTransaction->id]);

            return [
                'debit_transaction' => $debitTransaction,
                'credit_transaction' => $creditTransaction,
            ];
        });
    }

    /**
     * Lock the wallet.
     *
     * @param string|null $reason
     * @param \DateTimeInterface|null $until
     * @return bool
     */
    public function lock(?string $reason = null, ?\DateTimeInterface $until = null): bool
    {
        $this->is_locked = true;
        $this->lock_reason = $reason;
        $this->locked_until = $until;
        return $this->save();
    }

    /**
     * Unlock the wallet.
     *
     * @return bool
     */
    public function unlock(): bool
    {
        $this->is_locked = false;
        $this->lock_reason = null;
        $this->locked_until = null;
        return $this->save();
    }

    /**
     * Activate the wallet.
     *
     * @return bool
     */
    public function activate(): bool
    {
        $this->_status = self::ACTIVE;
        $this->activated_at = $this->activated_at ?? now();
        return $this->save();
    }

    /**
     * Deactivate the wallet.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        $this->_status = self::INACTIVE;
        return $this->save();
    }

    /**
     * Suspend the wallet.
     *
     * @param string|null $reason
     * @return bool
     */
    public function suspend(?string $reason = null): bool
    {
        $this->_status = self::SUSPENDED;
        $this->suspended_at = now();
        $this->metadata = array_merge($this->metadata ?? [], ['suspension_reason' => $reason]);
        return $this->save();
    }

    /**
     * Get the transaction history for the wallet.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTransactionHistory(array $filters = [], int $perPage = 15)
    {
        $query = $this->transactions()->latest();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['_status'])) {
            $query->where('_status', $filters['_status']);
        }

        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['min_amount'])) {
            $query->where('amount', '>=', $filters['min_amount']);
        }

        if (!empty($filters['max_amount'])) {
            $query->where('amount', '<=', $filters['max_amount']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }
}
