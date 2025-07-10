<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutboundEmailMessage extends Model
{
    use HasFactory, SoftDeletes;

    // Email statuses (_status column - string)
    const STATUS_DRAFT = 0;
    const STATUS_QUEUED = 1;
    const STATUS_SENT = 2;
    const STATUS_DELIVERED = 3;
    const STATUS_BOUNCED = 4;
    const STATUS_FAILED = 5;

    /**
     * Get all available email statuses
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_QUEUED => 'Queued',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_BOUNCED => 'Bounced',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * Get status value by label (case-insensitive)
     */
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
     * @var array
     */
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['communication', 'type', 'category'];

    protected $fillable = [
        'uuid',
        'communication_id',
        'type_id',
        'category_id',
        'outbound_bulk_text_message_id',
        'message_id',
        'subject',
        'html_content',
        'text_content',
        'from',
        'to',
        'cc',
        'bcc',
        'reply_to',
        '_status',  // Email status: draft, queued, sent, delivered, bounced, failed
        'sent_at',
        'delivered_at',
        'opened_at',
        'open_count',
        'clicked_at',
        'click_count',
        'attachments',
        'headers',
        'metadata',
        'session_id',
        'session_amount',
        'failure_reason',
        'error_code'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'from' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'reply_to' => 'array',
        'attachments' => 'array',
        'headers' => 'array',
        'metadata' => 'array',
        'open_count' => 'integer',
        'click_count' => 'integer',
        'session_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime'
    ];
    
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        '_status' => self::STATUS_DRAFT,  // Default status
        'open_count' => 0,
        'click_count' => 0,
        'session_amount' => 0,
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\OutboundEmailMessageRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\OutboundEmailMessageResource::class;
    }

    /**
     * Get the validation rules for creating a new outbound email message.
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_email_messages', 'uuid')],
            'communication_id' => 'required|exists:communications,id',
            'type_id' => 'required|exists:communication_types,id',
            'category_id' => 'required|exists:communication_categories,id',
            'outbound_bulk_text_message_id' => 'nullable|exists:outbound_bulk_text_messages,id',
            'message_id' => 'required|string|unique:outbound_email_messages,message_id',
            'subject' => 'required|string|max:998', // RFC 2822 limit
            'html_content' => 'nullable|string',
            'text_content' => 'nullable|string',
            'from' => 'required|json',
            'to' => 'required|json',
            'cc' => 'nullable|json',
            'bcc' => 'nullable|json',
            'reply_to' => 'nullable|json',
            '_status' => 'nullable|string|in:' . implode(',', [
                self::STATUS_DRAFT,
                self::STATUS_QUEUED,
                self::STATUS_SENT,
                self::STATUS_DELIVERED,
                self::STATUS_BOUNCED,
                self::STATUS_FAILED,
            ]),
            'sent_at' => 'nullable|date',
            'delivered_at' => 'nullable|date|after_or_equal:sent_at',
            'opened_at' => 'nullable|date|after_or_equal:sent_at',
            'open_count' => 'nullable|integer|min:0',
            'clicked_at' => 'nullable|date|after_or_equal:sent_at',
            'click_count' => 'nullable|integer|min:0',
            'attachments' => 'nullable|json',
            'headers' => 'nullable|json',
            'metadata' => 'nullable|json',
            'session_id' => 'nullable|string|unique:outbound_email_messages,session_id',
            'session_amount' => 'nullable|numeric|min:0',
            'failure_reason' => 'nullable|string',
            'error_code' => 'nullable|integer',
        ];
    }

    /**
     * Get the validation rules for updating an outbound email message.
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_email_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'outbound_bulk_text_message_id' => 'nullable|exists:outbound_bulk_text_messages,id',
            'message_id' => 'sometimes|string|unique:outbound_email_messages,message_id,' . $id,
            'subject' => 'sometimes|string|max:998',
            'html_content' => 'nullable|string',
            'text_content' => 'nullable|string',
            'from' => 'sometimes|json',
            'to' => 'sometimes|json',
            'cc' => 'nullable|json',
            'bcc' => 'nullable|json',
            'reply_to' => 'nullable|json',
            '_status' => 'sometimes|string|in:' . implode(',', array_keys(self::getStatusOptions())),
            'sent_at' => 'nullable|date',
            'delivered_at' => 'nullable|date|after_or_equal:sent_at',
            'opened_at' => 'nullable|date|after_or_equal:sent_at',
            'open_count' => 'nullable|integer|min:0',
            'clicked_at' => 'nullable|date|after_or_equal:sent_at',
            'click_count' => 'nullable|integer|min:0',
            'attachments' => 'nullable|json',
            'headers' => 'nullable|json',
            'metadata' => 'nullable|json',
            'session_id' => 'nullable|string|unique:outbound_email_messages,session_id,' . $id,
            'session_amount' => 'nullable|numeric|min:0',
            'failure_reason' => 'nullable|string',
            'error_code' => 'nullable|integer',

        ];
    }
    
    /**
     * Get the validation rules for sending an email.
     */
    public static function sendRules(): array
    {
        return [
            'subject' => 'required|string|max:998',
            'html_content' => 'required_without:text_content|string',
            'text_content' => 'required_without:html_content|string',
            'from' => 'required|array',
            'from.email' => 'required|email',
            'from.name' => 'nullable|string|max:255',
            'to' => 'required|array|min:1',
            'to.*.email' => 'required|email',
            'to.*.name' => 'nullable|string|max:255',
            'cc' => 'sometimes|array',
            'cc.*.email' => 'required_with:cc|email',
            'cc.*.name' => 'nullable|string|max:255',
            'bcc' => 'sometimes|array',
            'bcc.*.email' => 'required_with:bcc|email',
            'bcc.*.name' => 'nullable|string|max:255',
            'reply_to' => 'sometimes|array',
            'reply_to.email' => 'required_with:reply_to|email',
            'reply_to.name' => 'nullable|string|max:255',
            'attachments' => 'sometimes|array',
            'attachments.*.name' => 'required_with:attachments|string|max:255',
            'attachments.*.content' => 'required_with:attachments|string',
            'attachments.*.content_type' => 'required_with:attachments|string',
        ];
    }
    
    /**
     * Get the communication that owns this email message.
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    /**
     * Get the bulk text message associated with this email.
     */
    public function outboundBulkTextMessage(): BelongsTo
    {
        return $this->belongsTo(OutboundBulkTextMessage::class);
    }

    /**
     * Get the communication type that owns the outbound email message.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }

    /**
     * Get the communication category that owns the outbound email message.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }
    
    /**
     * Scope a query to only include sent emails.
     */
    public function scopeSent($query)
    {
        return $query->where('_status', self::STATUS_SENT);
    }
    
    /**
     * Scope a query to only include delivered emails.
     */
    public function scopeDelivered($query)
    {
        return $query->where('_status', self::STATUS_DELIVERED);
    }
    
    /**
     * Scope a query to only include processing emails.
     */
    public function scopeProcessing($query)
    {
        return $query->where('_status', self::STATUS_QUEUED);
    }
    
    /**
     * Scope a query to only include failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->where('_status', self::STATUS_FAILED);
    }
    
    /**
     * Scope a query to only include bounced emails.
     */
    public function scopeBounced($query)
    {
        return $query->where('_status', self::STATUS_BOUNCED);
    }
    
    /**
     * Check if the email has been opened.
     */
    public function isOpened(): bool
    {
        return $this->open_count > 0;
    }
    
    /**
     * Check if the email has been clicked.
     */
    public function isClicked(): bool
    {
        return $this->click_count > 0;
    }
    
    /**
     * Mark the email as processing.
     */
    public function markAsProcessing(): bool
    {
        $this->_status = self::STATUS_QUEUED;
        return $this->save();
    }
    
    /**
     * Mark the email as processed.
     */
    public function markAsProcessed(): bool
    {
        $this->_status = self::STATUS_SENT;
        $this->sent_at = $this->sent_at ?? now();
        return $this->save();
    }
    
    /**
     * Mark the email as sent.
     */
    public function markAsSent(): bool
    {
        $this->_status = self::STATUS_SENT;
        $this->sent_at = $this->sent_at ?? now();
        return $this->save();
    }
    
    /**
     * Mark the email as delivered.
     */
    public function markAsDelivered(): bool
    {
        $this->_status = self::STATUS_DELIVERED;
        $this->delivered_at = $this->delivered_at ?? now();
        return $this->save();
    }
    
    /**
     * Mark the email as failed.
     */
    public function markAsFailed(string $reason = null, int $errorCode = null): bool
    {
        $this->_status = self::STATUS_FAILED;
        $this->failure_reason = $reason;
        $this->error_code = $errorCode;
        $this->sent_at = $this->sent_at ?? now();
        return $this->save();
    }
    
    /**
     * Record that the email was opened.
     */
    public function recordOpen(): bool
    {
        $updates = [
            'open_count' => $this->open_count + 1,
        ];

        if (!$this->opened_at) {
            $updates['opened_at'] = now();
        }

        return $this->update($updates);
    }
    
    /**
     * Record that a link in the email was clicked.
     */
    public function recordClick(): bool
    {
        $updates = [
            'click_count' => $this->click_count + 1,
        ];
        
        if (is_null($this->clicked_at)) {
            $updates['clicked_at'] = now();
        }
        
        return $this->update($updates);
    }
}
