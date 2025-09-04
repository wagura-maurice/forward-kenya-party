<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    // Transaction channel constants
    const C2B = 0; // client to business
    const B2C = 1; // business to client
    const B2B = 2; // business to business

    public static function getChannelOptions()
    {
        return [
            self::C2B => 'C2B',
            self::B2C => 'B2C',
            self::B2B => 'B2B',
        ];
    }

    public static function getChannelValueByLabel(string $label)
    {
        $statusOptions = self::getChannelOptions();
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($statusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    // Transaction aggregator constants
    const MPESA_KE = 0;
    const AIRTEL_MONEY_KE = 1;
    const EQUITY_BANK_KE = 2;
    const KCB_BANK_KE = 3;
    const CASH = 4;
    const CRYPTO = 5;

    public static function getAggregatorOptions()
    {
        return [
            self::MPESA_KE => 'MPESA KE',
            self::AIRTEL_MONEY_KE => 'AIRTEL MONEY KE',
            self::EQUITY_BANK_KE => 'EQUITY BANK KE',
            self::KCB_BANK_KE => 'KCB BANK KE',
            self::CASH => 'CASH',
            self::CRYPTO => 'CRYPTO',
        ];
    }

    public static function getAggregatorValueByLabel(string $label)
    {
        $statusOptions = self::getAggregatorOptions();
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($statusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    // Mpesa Daraja API Transaction type constants
    const LIPA_NA_MPESA_ONLINE = 'LipaNaMpesaOnline';
    const CUSTOMER_PAY_BILL_ONLINE = 'CustomerPayBillOnline';
    const CUSTOMER_BUY_GOODS_ONLINE = 'CustomerBuyGoodsOnline';
    const PROMOTION_PAYMENT = 'PromotionPayment';
    const ACCOUNT_BALANCE = 'AccountBalance';
    
    // Transaction direction constants
    const CREDIT = 'credit';
    const DEBIT = 'debit';
    
    // Status constants (numeric for backward compatibility)
    const PENDING = 0; // Transaction has been created but not yet sent for processing
    const PROCESSING = 1; // Transaction is currently being processed
    const PROCESSED = 2; // Transaction has been processed, but the outcome (success/failure) is not yet confirmed
    const REJECTED = 3; // Transaction was processed and explicitly rejected
    const ACCEPTED = 4; // Transaction was successfully processed and accepted
    const FAILED = 5; // Transaction failed after processing
    const CANCELLED = 6; // Transaction was cancelled by the user or system
    const REFUNDED = 7; // Transaction amount was refunded
    const DISPUTED = 8; // Transaction is under dispute
    const RESOLVED = 9; // Previously disputed transaction has been resolved
    
    // String status constants for Wallet model compatibility
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    
    // Map numeric statuses to string statuses for Wallet compatibility
    const STATUS_MAP = [
        self::PENDING => self::STATUS_PENDING,
        self::ACCEPTED => self::STATUS_COMPLETED,
        self::FAILED => self::STATUS_FAILED,
        self::CANCELLED => self::STATUS_CANCELLED,
        self::REFUNDED => self::STATUS_COMPLETED,
        self::RESOLVED => self::STATUS_COMPLETED,
    ];
    
    // Transaction type options
    public static function getTypeOptions(): array
    {
        return [
            self::CREDIT => 'Credit',
            self::DEBIT => 'Debit',
        ];
    }
    
    /**
     * Get status options with labels
     * 
     * @param bool $forWallet If true, returns simplified statuses for Wallet model
     * @return array
     */
    public static function getStatusOptions(bool $forWallet = false): array
    {
        if ($forWallet) {
            return [
                self::STATUS_PENDING => 'Pending',
                self::STATUS_COMPLETED => 'Completed',
                self::STATUS_FAILED => 'Failed',
                self::STATUS_CANCELLED => 'Cancelled',
            ];
        }
        
        return [
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::REJECTED => 'Rejected',
            self::ACCEPTED => 'Accepted',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::DISPUTED => 'Disputed',
            self::RESOLVED => 'Resolved',
        ];
    }

    public static function getMpesaDarajaAPITransactionTypeOptions()
    {
        return [
            self::LIPA_NA_MPESA_ONLINE => 'Lipa Na Mpesa Online',
            self::CUSTOMER_PAY_BILL_ONLINE => 'Customer Pay Bill Online',
            self::CUSTOMER_BUY_GOODS_ONLINE => 'Customer Buy Goods Online',
            self::PROMOTION_PAYMENT => 'Promotion Payment',
            self::ACCOUNT_BALANCE => 'Account Balance',
        ];
    }

    public static function getMpesaDarajaAPITransactionTypeValueByLabel(string $label)
    {
        $statusOptions = self::getMpesaDarajaAPITransactionTypeOptions();
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($statusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    /**
     * Convert numeric status to string status for Wallet compatibility
     * 
     * @param int $status Numeric status
     * @return string
     */
    public static function toWalletStatus(int $status): string
    {
        return self::STATUS_MAP[$status] ?? self::STATUS_PENDING;
    }
    
    /**
     * Convert string status to numeric status
     * 
     * @param string $status String status
     * @return int
     */
    public static function fromWalletStatus(string $status): int
    {
        $map = array_flip(self::STATUS_MAP);
        return $map[$status] ?? self::PENDING;
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
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'party_a',
        'party_b',
        'account_reference',
        'transaction_api',
        'transaction_channel',
        'transaction_aggregator',
        'transaction_id',
        'transaction_amount',
        'transaction_code',
        'transaction_timestamp',
        'transaction_details',
        'collect',
        '_response',
        '_status',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_timestamp' => 'datetime',
        'transaction_amount' => 'decimal:2',
        '_response' => 'array',
        '_status' => 'integer',
    ];

    /**
     * @property string $transaction_details Additional details or remarks about the transaction
     * @property string|null $collect Collection reference or identifier for tracking related transactions
     * @property array|null $_response Full JSON response from the payment processor
     * @property int $_status Default status is pending
     */

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\TransactionRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\TransactionResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['required', 'uuid', Rule::unique('transactions', 'uuid')],
            'type_id' => ['required', 'integer', 'exists:invoice_types,id'],
            'category_id' => ['required', 'integer', 'exists:invoice_categories,id'],
            'party_a' => ['required', 'string'],
            'party_b' => ['required', 'string'],
            'account_reference' => ['required', 'string', 'exists:invoices,uuid'],
            'transaction_API' => ['nullable', 'string', 'max:255'],
            'transaction_channel' => ['required', 'integer'],
            'transaction_aggregator' => ['required', 'integer'],
            'transaction_id' => ['required', 'string', Rule::unique('transactions', 'transaction_id')],
            'transaction_amount' => ['nullable', 'numeric', 'min:0'],
            'transaction_code' => ['nullable', 'string', Rule::unique('transactions', 'transaction_code')],
            'transaction_timestamp' => ['required', 'date'],
            'transaction_details' => ['nullable', 'string'],
            'collect' => ['nullable', 'string', 'max:50'],
            '_response' => ['nullable', 'json'],
            '_status' => ['nullable', 'integer'],
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('transactions', 'uuid')->ignore($id)],
            'type_id' => ['nullable', 'integer', 'exists:invoice_types,id'],
            'category_id' => ['nullable', 'integer', 'exists:invoice_categories,id'],
            'party_a' => ['nullable', 'string'],
            'party_b' => ['nullable', 'string'],
            'account_reference' => ['nullable', 'string', 'exists:invoices,uuid'],
            'transaction_API' => ['nullable', 'string', 'max:255'],
            'transaction_channel' => ['nullable', 'integer'],
            'transaction_aggregator' => ['nullable', 'integer'],
            'transaction_id' => ['nullable', 'string', Rule::unique('transactions', 'transaction_id')->ignore($id)],
            'transaction_amount' => ['nullable', 'numeric', 'min:0'],
            'transaction_code' => ['nullable', 'string', Rule::unique('transactions', 'transaction_code')->ignore($id)],
            'transaction_timestamp' => ['nullable', 'date'],
            'transaction_details' => ['nullable', 'string'],
            'collect' => ['nullable', 'string', 'max:50'],
            '_response' => ['nullable', 'json'],
            '_status' => ['nullable', 'integer'],
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InvoiceType::class, 'type_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InvoiceCategory::class, 'category_id', 'id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'account_reference', 'uuid');
    }
}
