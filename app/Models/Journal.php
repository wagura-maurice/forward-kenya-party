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
 * App\Models\Journal
 *
 * @property int $id
 * @property string $uuid
 * @property string|null $reference_number
 * @property int|null $transaction_id
 * @property int $account_debited
 * @property int $account_credited
 * @property int|null $created_by
 * @property int|null $approved_by
 * @property int|null $linked_journal_id
 * @property float $amount
 * @property string $currency
 * @property float $exchange_rate
 * @property int $journal_type
 * @property int $status
 * @property string $posting_date
 * @property string $value_date
 * @property string|null $description
 * @property string|null $notes
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $posted_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Account $accountCredited
 * @property-read \App\Models\Account $accountDebited
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Journal|null $linkedJournal
 * @property-read \App\Models\Transaction|null $transaction
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ledger[] $ledgers
 * @property-read int|null $ledgers_count
 */
class Journal extends Model
{
    use HasFactory, SoftDeletes;

    // Journal Type constants
    public const OPERATIONAL = 0;
    public const ADJUSTMENT = 1;
    public const ACCRUAL = 2;
    public const REVERSAL = 3;
    public const CLOSING = 4;
    public const OPENING = 5;
    public const TRANSFER = 6;
    public const TAX = 7;
    
    // Journal Status constants
    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;
    public const STATUS_POSTED = 3;

    /**
     * Get journal type options with labels
     * 
     * @return array
     */
    public static function getJournalTypeOptions(): array
    {
        return [
            self::OPERATIONAL => 'Operational',
            self::ADJUSTMENT => 'Adjustment',
            self::ACCRUAL => 'Accrual',
            self::REVERSAL => 'Reversal',
            self::CLOSING => 'Closing',
            self::OPENING => 'Opening',
            self::TRANSFER => 'Transfer',
            self::TAX => 'Tax',
        ];
    }
    
    /**
     * Get journal status options with labels
     * 
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_POSTED => 'Posted',
        ];
    }

    public static function getJournalTypeValueByLabel(string $label)
    {
        $statusOptions = self::getJournalTypeOptions();
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
        'account_debited',
        'account_credited',
        'created_by',
        'approved_by',
        'linked_journal_id',
        'amount',
        'currency',
        'exchange_rate',
        'journal_type',
        '_status',
        'posting_date',
        'value_date',
        'description',
        'notes',
        'metadata',
        'approved_at',
        'posted_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'posting_date' => 'date',
        'value_date' => 'datetime',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
        'metadata' => 'array',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'journal_type_label',
        'status_label',
        'formatted_amount',
    ];
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($journal) {
            if (empty($journal->uuid)) {
                $journal->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            
            if (empty($journal->reference_number)) {
                $journal->reference_number = static::generateReferenceNumber();
            }
            
            if (empty($journal->created_by) && Auth::check()) {
                $journal->created_by = Auth::id();
            }
            
            if (empty($journal->posting_date)) {
                $journal->posting_date = now()->toDateString();
            }
            
            if (empty($journal->value_date)) {
                $journal->value_date = now();
            }
            
            if (empty($journal->status)) {
                $journal->status = self::STATUS_PENDING;
            }
        });
        
        static::created(function ($journal) {
            // Create corresponding ledger entries when a journal is created
            $journal->createLedgerEntries();
        });
    }

    /**
     * Get the request class for the model.
     */
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\JournalRequest::class;
    }

    /**
     * Get the resource class for the model.
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\JournalResource::class;
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
            'reference_number' => ['nullable', 'string', 'max:100', 'unique:journals,reference_number'],
            'transaction_id' => ['nullable', 'integer', 'exists:transactions,id'],
            'account_debited' => ['required', 'integer', 'exists:accounts,id'],
            'account_credited' => ['required', 'integer', 'exists:accounts,id', 'different:account_debited'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'approved_by' => ['nullable', 'integer', 'exists:users,id'],
            'linked_journal_id' => ['nullable', 'integer', 'exists:journals,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['required', 'numeric', 'min:0', 'max:999999.999999'],
            'journal_type' => ['required', 'integer', 'in:' . implode(',', array_keys(self::getJournalTypeOptions()))],
            '_status' => ['sometimes', 'integer', 'in:' . implode(',', array_keys(self::getStatusOptions()))],
            'posting_date' => ['required', 'date'],
            'value_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'approved_at' => ['nullable', 'date'],
            'posted_at' => ['nullable', 'date'],
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
            'uuid' => ['nullable', 'string', 'uuid', 'max:36', Rule::unique('journals', 'uuid')->ignore($id)],
            'reference_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('journals', 'reference_number')->ignore($id)
            ],
            'account_debited' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'account_credited' => ['sometimes', 'required', 'integer', 'exists:accounts,id', 'different:account_debited'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the transaction that owns the journal entry.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
    
    /**
     * Get the user who created the journal entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the user who approved the journal entry.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the account that was debited.
     */
    public function accountDebited(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_debited');
    }

    /**
     * Get the account that was credited.
     */
    public function accountCredited(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_credited');
    }

    /**
     * Get the linked journal entry.
     */
    public function linkedJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'linked_journal_id');
    }
    
    /**
     * Get the ledger entries for this journal.
     */
    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class, 'journal_id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the journal type label.
     */
    public function getJournalTypeLabelAttribute(): string
    {
        return self::getJournalTypeOptions()[$this->journal_type] ?? 'Unknown';
    }
    
    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->_status] ?? 'Unknown';
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
        
        return "{$symbol} {$amount}";
    }
    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    
    /**
     * Scope a query to only include entries for a specific account (debited or credited).
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_debited', $accountId)
                    ->orWhere('account_credited', $accountId);
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
     * Scope a query to only include entries of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('journal_type', $type);
    }
    
    /**
     * Scope a query to only include entries with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('_status', $status);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */
    
    /**
     * Approve the journal entry.
     *
     * @param int $userId
     * @return bool
     */
    public function approve(int $userId): bool
    {
        return $this->update([
            '_status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }
    
    /**
     * Reject the journal entry.
     *
     * @return bool
     */
    public function reject(): bool
    {
        return $this->update([
            '_status' => self::STATUS_REJECTED,
        ]);
    }
    
    /**
     * Mark the journal entry as posted.
     *
     * @return bool
     */
    public function markAsPosted(): bool
    {
        return $this->update([
            '_status' => self::STATUS_POSTED,
            'posted_at' => now(),
        ]);
    }
    
    /**
     * Create ledger entries for this journal.
     *
     * @return void
     */
    public function createLedgerEntries(): void
    {
        // Create debit ledger entry
        $this->ledgers()->create([
            'transaction_id' => $this->transaction_id,
            'account_id' => $this->account_debited,
            'entry_type' => Ledger::DEBIT,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate,
            'description' => $this->description,
            'posting_date' => $this->posting_date,
            'reference_number' => $this->reference_number . '-D',
        ]);
        
        // Create credit ledger entry
        $this->ledgers()->create([
            'transaction_id' => $this->transaction_id,
            'account_id' => $this->account_credited,
            'entry_type' => Ledger::CREDIT,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'exchange_rate' => $this->exchange_rate,
            'description' => $this->description,
            'posting_date' => $this->posting_date,
            'reference_number' => $this->reference_number . '-C',
        ]);
    }
    
    /**
     * Generate a unique reference number for the journal entry.
     *
     * @return string
     */
    public static function generateReferenceNumber(): string
    {
        $prefix = 'JRN' . date('Ymd');
        $lastJournal = static::where('reference_number', 'like', "{$prefix}%")
                            ->orderBy('id', 'desc')
                            ->first();
        
        if ($lastJournal) {
            $lastNumber = (int) substr($lastJournal->reference_number, -6);
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '000001';
        }
        
        return "{$prefix}{$nextNumber}";
    }
    
    /**
     * Get the balance for an account up to a specific date.
     *
     * @param int $accountId
     * @param string|null $date
     * @return array
     */
    public static function getAccountBalance(int $accountId, ?string $date = null): array
    {
        $query = static::where(function($q) use ($accountId) {
            $q->where('account_debited', $accountId)
              ->orWhere('account_credited', $accountId);
        });
        
        if ($date) {
            $query->where('posting_date', '<=', $date);
        }
        
        $debits = (float) $query->clone()
            ->where('account_debited', $accountId)
            ->sum('amount');
            
        $credits = (float) $query->clone()
            ->where('account_credited', $accountId)
            ->sum('amount');
        
        return [
            'debits' => $debits,
            'credits' => $credits,
            'balance' => $debits - $credits,
        ];
    }
}
