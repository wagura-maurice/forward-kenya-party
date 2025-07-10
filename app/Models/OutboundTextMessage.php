<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutboundTextMessage extends Model
{
    use HasFactory, SoftDeletes;

    // Message statuses
    const STATUS_DRAFT = 0;
    const STATUS_PENDING = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_SENT = 3;
    const STATUS_DELIVERED = 4;
    const STATUS_FAILED = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'communication_id',
        'type_id',
        'category_id',
        'outbound_bulk_text_message_id',
        'bulk_text_message_id',
        'content',
        'telephone',
        'message_id',
        '_status',        // Message status: pending, processing, sent, delivered, failed
        'network_code',
        'failure_reason',
        'error_code',
        'session_id',
        'session_amount',
        'sent_at',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'session_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        '_status' => self::STATUS_PENDING,
        'session_amount' => 0,
    ];

    /**
     * Get the status options for the message.
     *
     * @return array
     */
    /**
     * Get all available status options for display.
     *
     * @return array
     */
    public function getStatusAttribute($value)
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_FAILED => 'Failed',
        ];

        return $statuses[$value] ?? 'Unknown';
    }

    /**
     * Get all available message statuses.
     *
     * @return array
     */
    public static function getMessageStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * Mark the message as processing.
     *
     * @return bool
     */
    public function markAsProcessing()
    {
        $this->_status = self::STATUS_PROCESSING;
        $this->save();
        return $this;
    }

    /**
     * Mark the message as processed.
     *
     * @return bool
     */
    public function markAsProcessed()
    {
        $this->_status = self::STATUS_SENT;
        $this->save();
        return $this;
    }

    /**
     * Mark the message as sent.
     *
     * @return bool
     */
    public function markAsSent()
    {
        $this->_status = self::STATUS_SENT;
        $this->sent_at = now();
        $this->save();
        return $this;
    }

    /**
     * Mark the message as delivered.
     *
     * @return bool
     */
    public function markAsDelivered()
    {
        $this->_status = self::STATUS_DELIVERED;
        $this->delivered_at = now();
        $this->save();
        return $this;
    }

    /**
     * Mark the message as failed.
     *
     * @param string|null $reason
     * @param int|null $errorCode
     * @return bool
     */
    public function markAsFailed($error = null)
    {
        $this->_status = self::STATUS_FAILED;
        if ($error) {
            $this->error_code = $error->getCode();
            $this->failure_reason = $error->getMessage();
        }
        $this->save();
        return $this;
    }

    /**
     * Check if the message was sent successfully.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->_status === self::STATUS_PENDING;
    }

    public function isProcessing()
    {
        return $this->_status === self::STATUS_PROCESSING;
    }

    public function isSent()
    {
        return $this->_status === self::STATUS_SENT;
    }

    public function isProcessed()
    {
        return $this->_status === self::STATUS_SENT;
    }

    public function isDelivered()
    {
        return $this->_status === self::STATUS_DELIVERED;
    }

    public function isFailed()
    {
        return $this->_status === self::STATUS_FAILED;
    }

    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_text_messages', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'required|exists:communication_types,id',
            'category_id' => 'required|exists:communication_categories,id',
            'outbound_bulk_text_message_id' => 'nullable|exists:outbound_bulk_text_messages,id',
            'bulk_text_message_id' => 'required|exists:bulk_text_messages,id',
            'content' => 'required|string',
            'telephone' => 'required|string|max:20',
            'message_id' => 'nullable|string|unique:outbound_text_messages,message_id',
            '_status' => 'nullable|string|in:' . implode(',', [
                self::STATUS_PENDING,
                self::STATUS_PROCESSING,
                self::STATUS_SENT,
                self::STATUS_DELIVERED,
                self::STATUS_FAILED
            ]),
            'network_code' => 'nullable|string|max:50',
            'failure_reason' => 'nullable|string',
            'error_code' => 'nullable|string',
            'session_id' => 'nullable|string|unique:outbound_text_messages,session_id',
            'session_amount' => 'nullable|numeric|min:0',
            'sent_at' => 'nullable|date',
            'delivered_at' => 'nullable|date|after_or_equal:sent_at',
        ];
    }

    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_text_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'outbound_bulk_text_message_id' => 'nullable|exists:outbound_bulk_text_messages,id',
            'bulk_text_message_id' => 'nullable|exists:bulk_text_messages,id',
            'content' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'message_id' => 'nullable|string|unique:outbound_text_messages,message_id,' . $id,
            '_status' => 'nullable|string|in:' . implode(',', [
                self::STATUS_PENDING,
                self::STATUS_PROCESSING,
                self::STATUS_SENT,
                self::STATUS_DELIVERED,
                self::STATUS_FAILED
            ]),
            'network_code' => 'nullable|string|max:50',
            'failure_reason' => 'nullable|string',
            'error_code' => 'nullable|string',
            'session_id' => 'nullable|string|unique:outbound_text_messages,session_id,' . $id,
            'session_amount' => 'nullable|numeric|min:0',
            'sent_at' => 'nullable|date',
            'delivered_at' => 'nullable|date|after_or_equal:sent_at',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }

    /**
     * Get the bulk text message that this message belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function outboundBulkTextMessage(): BelongsTo
    {
        return $this->belongsTo(OutboundBulkTextMessage::class, 'outbound_bulk_text_message_id');
    }

    /**
     * Scope a query to only include pending messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('_status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include sent messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing($query)
    {
        return $query->where('_status', self::STATUS_PROCESSING);
    }

    /**
     * Scope a query to only include delivered messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSent($query)
    {
        return $query->where('_status', self::STATUS_SENT);
    }

    /**
     * Scope a query to only include processed messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessed($query)
    {
        return $query->where('_status', self::STATUS_SENT);
    }

    /**
     * Scope a query to only include delivered messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDelivered($query)
    {
        return $query->where('_status', self::STATUS_DELIVERED);
    }

    /**
     * Scope a query to only include failed messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('_status', self::STATUS_FAILED);
    }
}
