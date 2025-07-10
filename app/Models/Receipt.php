<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const PAID = 1;
    const OVERDUE = 2;
    const PARTIALLY_PAID = 3;
    const CANCELLED = 4;
    const REFUNDED = 5;

    // Payment method constants
    const METHOD_CASH = 'cash';
    const METHOD_MPESA = 'mpesa';
    const METHOD_CARD = 'card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CHEQUE = 'cheque';
    const METHOD_OTHER = 'other';

    // Default currency
    const DEFAULT_CURRENCY = 'KES';

    /**
     * Get all available status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        ];
    }
    
    /**
     * Get all available payment methods.
     */
    public static function getPaymentMethodOptions(): array
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_MPESA => 'M-Pesa',
            self::METHOD_CARD => 'Credit/Debit Card',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_CHEQUE => 'Cheque',
            self::METHOD_OTHER => 'Other',
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
        'invoice_id',
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
        'quantity',
        'unit_price',
        'issued_at',
        'paid_at',
        'approved_at',
        'due_at',
        'payment_method',
        'transaction_reference',
        'collect',
        'description',
        'notes',
        'metadata',
        '_status',
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
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'due_at' => 'datetime',
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
        static::creating(function ($receipt) {
            if (empty($receipt->reference_number)) {
                $receipt->reference_number = static::generateReferenceNumber();
            }
            
            if (empty($receipt->currency)) {
                $receipt->currency = self::DEFAULT_CURRENCY;
            }
            
            if (empty($receipt->issued_at)) {
                $receipt->issued_at = now();
            }
            
            if (empty($receipt->exchange_rate)) {
                $receipt->exchange_rate = 1.0;
            }
            
            // Calculate total with tax if not set
            if (empty($receipt->total_with_tax) && !empty($receipt->payable)) {
                $receipt->total_with_tax = $receipt->calculateTotalWithTax();
            }
            
            // Calculate balance
            if (empty($receipt->balance) && isset($receipt->total_with_tax) && isset($receipt->paid)) {
                $receipt->balance = max(0, $receipt->total_with_tax - $receipt->paid);
            }
        });
        
        static::saving(function ($receipt) {
            // Update status based on payment
            if ($receipt->isDirty('paid')) {
                $receipt->balance = max(0, $receipt->total_with_tax - $receipt->paid);
                
                if ($receipt->balance <= 0 && $receipt->paid > 0) {
                    $receipt->_status = self::PAID;
                    $receipt->paid_at = $receipt->paid_at ?? now();
                } elseif ($receipt->paid > 0) {
                    $receipt->_status = self::PARTIALLY_PAID;
                }
            }
            
            // Update status if due date has passed
            if ($receipt->due_at && $receipt->due_at->isPast() && 
                !in_array($receipt->_status, [self::PAID, self::CANCELLED, self::REFUNDED])) {
                $receipt->_status = self::OVERDUE;
            }
        });
    }

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\ReceiptRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ReceiptResource::class;
    }

    /**
     * Get the validation rules for creating a new receipt.
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'uuid', 'max:36', Rule::unique('receipts', 'uuid')],
            'reference_number' => ['nullable', 'string', 'max:50', Rule::unique('receipts', 'reference_number')],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'type_id' => ['required', 'integer', 'exists:receipt_types,id'],
            'category_id' => ['required', 'integer', 'exists:receipt_categories,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'approved_by' => ['nullable', 'integer', 'exists:users,id'],
            'payable' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'paid' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'balance' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'total_with_tax' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'currency' => ['nullable', 'string', 'size:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0', 'max:999999.999999'],
            'quantity' => ['nullable', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'unit_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'issued_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'approved_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'collect' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:65535'],
            'notes' => ['nullable', 'string', 'max:65535'],
            'metadata' => ['nullable', 'array'],
            '_status' => ['required', 'integer', 'in:' . implode(',', array_keys(self::getStatusOptions()))],
        ];
    }

    /**
     * Get the validation rules for updating an existing receipt.
     */
    public static function updateRules(int $id): array
    {
        return array_merge(self::createRules(), [
            'uuid' => ['nullable', 'string', 'uuid', 'max:36', Rule::unique('receipts', 'uuid')->ignore($id)],
            'reference_number' => [
                'nullable', 
                'string', 
                'max:50', 
                Rule::unique('receipts', 'reference_number')->ignore($id)
            ],
            'type_id' => ['sometimes', 'required', 'integer', 'exists:receipt_types,id'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:receipt_categories,id'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the receipt type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ReceiptType::class, 'type_id');
    }

    /**
     * Get the receipt category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ReceiptCategory::class, 'category_id');
    }

    /**
     * Get the user who made the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the user who approved the receipt.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the associated invoice, if any.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the status label for the receipt.
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
     * Check if the receipt is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at && 
               $this->due_at->isPast() && 
               !in_array($this->_status, [self::PAID, self::CANCELLED, self::REFUNDED]);
    }
    
    /**
     * Check if the receipt is fully paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->_status === self::PAID || 
               ($this->balance <= 0 && $this->paid > 0);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    
    /**
     * Scope a query to only include overdue receipts.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())
                    ->whereNotIn('_status', [self::PAID, self::CANCELLED, self::REFUNDED]);
    }
    
    /**
     * Scope a query to only include paid receipts.
     */
    public function scopePaid($query)
    {
        return $query->where('_status', self::PAID)
                    ->orWhere(function($q) {
                        $q->where('balance', '<=', 0)
                          ->where('paid', '>', 0);
                    });
    }
    
    /**
     * Scope a query to only include unpaid receipts.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('balance', '>', 0)
                    ->where('_status', '!=', self::CANCELLED);
    }
    
    /**
     * Scope a query to only include receipts for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Scope a query to only include receipts with a specific status.
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
     * Apply a payment to the receipt.
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
        $this->payment_method = $paymentMethod;
        $this->transaction_reference = $reference;
        
        // Update status based on payment
        if ($this->balance <= 0) {
            $this->_status = self::PAID;
            $this->paid_at = now();
        } else if ($this->paid > 0) {
            $this->_status = self::PARTIALLY_PAID;
        }
        
        return $this->save();
    }
    
    /**
     * Mark the receipt as approved.
     *
     * @param int $userId
     * @return bool
     */
    public function approve(int $userId): bool
    {
        return $this->update([
            'approved_by' => $userId,
            'approved_at' => now(),
            '_status' => $this->balance <= 0 ? self::PAID : $this->_status,
        ]);
    }
    
    /**
     * Generate a unique reference number for the receipt.
     */
    public static function generateReferenceNumber(): string
    {
        $prefix = 'RCPT' . date('Ymd');
        $lastReceipt = static::where('reference_number', 'like', "{$prefix}%")
                            ->orderBy('id', 'desc')
                            ->first();
        
        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->reference_number, -6);
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '000001';
        }
        
        return "{$prefix}{$nextNumber}";
    }
}
