<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    // Invoice Status constants
    const PENDING = 0;
    const SENT = 1;
    const VIEWED = 2;
    const PROCESSING = 3;
    const PROCESSED = 4;
    const DISPUTED = 5;
    const PARTIAL = 6;
    const OVERDUE = 7;
    const CANCELLED = 8;
    const REFUNDED = 9;
    const SETTLED = 10;

    // Payment term constants
    const PAYMENT_TERM_NET_7 = 'net_7';
    const PAYMENT_TERM_NET_15 = 'net_15';
    const PAYMENT_TERM_NET_30 = 'net_30';
    const PAYMENT_TERM_NET_60 = 'net_60';
    const PAYMENT_TERM_DUE_ON_RECEIPT = 'due_on_receipt';
    const PAYMENT_TERM_END_OF_MONTH = 'end_of_month';
    const PAYMENT_TERM_END_OF_NEXT_MONTH = 'end_of_next_month';

    // Default currency
    const DEFAULT_CURRENCY = 'KES';

    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::SENT => 'Sent',
            self::VIEWED => 'Viewed',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::DISPUTED => 'Disputed',
            self::PARTIAL => 'Partial',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::SETTLED => 'Settled',
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
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'reference_number',
        'type_id',
        'category_id',
        'user_id',
        'approved_by',
        'payable',
        'discount',
        'paid',
        'balance',
        'total_with_tax',
        'tax_rate',
        'currency',
        'exchange_rate',
        'issued_at',
        'due_at',
        'paid_at',
        'approved_at',
        'payment_terms',
        'payment_due_days',
        'collect',
        'description',
        'notes',
        '_receipts',
        '_statement',
        '_status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payable' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_with_tax' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_due_days' => 'integer',
        '_receipts' => 'array',
        '_statement' => 'array',
        'metadata' => 'array',
        '_status' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'formatted_amount',
        'is_overdue',
        'is_paid',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->reference_number)) {
                $invoice->reference_number = static::generateReferenceNumber();
            }
            
            if (empty($invoice->currency)) {
                $invoice->currency = self::DEFAULT_CURRENCY;
            }
            
            if (empty($invoice->issued_at)) {
                $invoice->issued_at = now();
            }
            
            if (empty($invoice->due_at) && !empty($invoice->payment_due_days)) {
                $invoice->due_at = now()->addDays($invoice->payment_due_days);
            }
            
            if (empty($invoice->exchange_rate)) {
                $invoice->exchange_rate = 1.0;
            }
            
            // Calculate total with tax if not set
            if (empty($invoice->total_with_tax) && !empty($invoice->payable)) {
                $invoice->total_with_tax = $invoice->calculateTotalWithTax();
            }
        });
        
        static::saving(function ($invoice) {
            // Update balance when paid amount changes
            if ($invoice->isDirty('paid')) {
                $invoice->balance = max(0, $invoice->total_with_tax - $invoice->paid);
                
                // Update status based on payment
                if ($invoice->balance <= 0 && $invoice->paid > 0) {
                    $invoice->_status = self::SETTLED;
                    $invoice->paid_at = $invoice->paid_at ?? now();
                } elseif ($invoice->paid > 0) {
                    $invoice->_status = self::PARTIAL;
                }
            }
            
            // Update status if due date has passed
            if ($invoice->due_at && $invoice->due_at->isPast() && 
                !in_array($invoice->_status, [self::PAID, self::CANCELLED, self::REFUNDED, self::SETTLED])) {
                $invoice->_status = self::OVERDUE;
            }
        });
    }

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\InvoiceRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\InvoiceResource::class;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'uuid', 'max:36', Rule::unique('invoices', 'uuid')],
            'reference_number' => ['nullable', 'string', 'max:50', Rule::unique('invoices', 'reference_number')],
            'type_id' => ['required', 'integer', 'exists:invoice_types,id'],
            'category_id' => ['required', 'integer', 'exists:invoice_categories,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'approved_by' => ['nullable', 'integer', 'exists:users,id'],
            'payable' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'paid' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'balance' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'total_with_tax' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'currency' => ['nullable', 'string', 'size:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0', 'max:999999.999999'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'paid_at' => ['nullable', 'date'],
            'approved_at' => ['nullable', 'date'],
            'payment_terms' => ['nullable', 'string', 'max:50'],
            'payment_due_days' => ['nullable', 'integer', 'min:0', 'max:3650'], // 10 years max
            'collect' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:65535'],
            'notes' => ['nullable', 'string', 'max:65535'],
            '_receipts' => ['nullable', 'array'],
            '_receipts.*' => ['uuid', 'exists:receipts,uuid'],
            '_statement' => ['nullable', 'array'],
            '_status' => ['required', 'integer', 'in:' . implode(',', array_keys(self::getStatusOptions()))],
            'metadata' => ['nullable', 'array'],
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
            'uuid' => ['nullable', 'string', 'uuid', 'max:36', Rule::unique('invoices', 'uuid')->ignore($id)],
            'reference_number' => [
                'nullable', 
                'string', 
                'max:50', 
                Rule::unique('invoices', 'reference_number')->ignore($id)
            ],
            'type_id' => ['sometimes', 'required', 'integer', 'exists:invoice_types,id'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:invoice_categories,id'],
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
        ]);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InvoiceType::class, 'type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InvoiceCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user who approved this invoice.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all receipts associated with this invoice.
     */
    public function receipts()
    {
        if (!isset($this->decoded_receipts)) {
            $this->decoded_receipts = is_string($this->_receipts) 
                ? json_decode($this->_receipts, true) 
                : $this->_receipts;
        }

        $receipts = $this->decoded_receipts;

        if (empty($receipts)) {
            return collect();
        }

        return Receipt::whereIn('uuid', $receipts)->get();
    }

    /**
     * Get all transactions related to this invoice.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'invoice_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the status label for the invoice.
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
        $amount = number_format($this->total_with_tax, 2);
        $currency = $this->currency;
        
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

    /**
     * Check if the invoice is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at && $this->due_at->isPast() && 
               !in_array($this->_status, [self::PAID, self::CANCELLED, self::REFUNDED, self::SETTLED]);
    }

    /**
     * Check if the invoice is fully paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->_status === self::SETTLED || 
               ($this->balance <= 0 && $this->paid > 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())
                    ->whereNotIn('_status', [self::PAID, self::CANCELLED, self::REFUNDED, self::SETTLED]);
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('_status', self::SETTLED)
                    ->orWhere(function($q) {
                        $q->where('balance', '<=', 0)
                          ->where('paid', '>', 0);
                    });
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('balance', '>', 0)
                    ->where('_status', '!=', self::CANCELLED);
    }

    /**
     * Scope a query to only include invoices for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include invoices with a specific status.
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
     * Calculate the total amount with tax.
     */
    public function calculateTotalWithTax(): float
    {
        $subtotal = $this->payable - $this->discount;
        $taxAmount = $subtotal * ($this->tax_rate / 100);
        return round($subtotal + $taxAmount, 2);
    }

    /**
     * Apply a payment to the invoice.
     *
     * @param float $amount
     * @param string $paymentMethod
     * @param string $reference
     * @return bool
     */
    public function applyPayment(float $amount, string $paymentMethod, string $reference): bool
    {
        $this->paid += $amount;
        $this->balance = max(0, $this->total_with_tax - $this->paid);
        
        // Update status based on payment
        if ($this->balance <= 0) {
            $this->_status = self::SETTLED;
            $this->paid_at = now();
        } else if ($this->paid > 0) {
            $this->_status = self::PARTIAL;
        }
        
        // Record the transaction
        $this->transactions()->create([
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            '_status' => 'completed',
        ]);
        
        return $this->save();
    }

    /**
     * Mark the invoice as approved.
     *
     * @param int $userId
     * @return bool
     */
    public function approve(int $userId): bool
    {
        return $this->update([
            'approved_by' => $userId,
            'approved_at' => now(),
            '_status' => $this->paid >= $this->total_with_tax ? self::SETTLED : self::APPROVED,
        ]);
    }

    /**
     * Generate a unique reference number for the invoice.
     */
    public static function generateReferenceNumber(): string
    {
        $prefix = 'INV' . date('Ymd');
        $lastInvoice = static::where('reference_number', 'like', "{$prefix}%")
                            ->orderBy('id', 'desc')
                            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->reference_number, -6);
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '000001';
        }
        
        return "{$prefix}{$nextNumber}";
    }
}
