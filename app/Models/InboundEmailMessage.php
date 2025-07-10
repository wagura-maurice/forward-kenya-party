<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InboundEmailMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inbound_email_messages';

    // Status constants
    const PENDING = 0;
    const PROCESSED = 1;
    const FAILED = 2;
    const QUEUED = 3;
    const IN_PROGRESS = 4;
    const COMPLETED = 5;
    const BUSY = 6;
    const NO_ANSWER = 7;

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
        'attachments' => 'array',
        'headers' => 'array',
        'dkim_verification' => 'array',
        'spf_verification' => 'array',
        'is_spam' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        '_status' => self::PENDING,
        'is_spam' => false,
        'session_amount' => 0,
    ];

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
        'from',
        'to',
        'cc',
        'bcc',
        'subject',
        'html_content',
        'text_content',
        'attachments',
        'message_id',
        'headers',
        'ip_address',
        'mail_server',
        'dkim_verification',
        'spf_verification',
        'is_spam',
        'spam_score',
        'session_id',
        'session_amount',
        'network_code',
        'failure_reason',
        'processed_at',
        '_status',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['communication', 'type', 'category'];

    /**
     * Get the status options for the message.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::PROCESSED => 'Processed',
            self::FAILED => 'Failed',
        ];
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
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Get the communication that owns the inbound email message.
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    /**
     * Get the communication type that owns the inbound email message.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }

    /**
     * Get the communication category that owns the inbound email message.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }

    /**
     * Scope a query to only include pending messages.
     */
    public function scopePending($query)
    {
        return $query->where('_status', self::PENDING);
    }

    /**
     * Scope a query to only include processed messages.
     */
    public function scopeProcessed($query)
    {
        return $query->where('_status', self::PROCESSED);
    }

    /**
     * Scope a query to only include failed messages.
     */
    public function scopeFailed($query)
    {
        return $query->where('_status', self::FAILED);
    }

    /**
     * Scope a query to only include spam messages.
     */
    public function scopeSpam($query)
    {
        return $query->where('is_spam', true);
    }

    /**
     * Mark the message as processed.
     *
     * @return bool
     */
    public function markAsProcessed(): bool
    {
        return $this->update([
            '_status' => self::PROCESSED,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark the message as failed.
     *
     * @param string|null $reason
     * @return bool
     */
    public function markAsFailed(?string $reason = null): bool
    {
        return $this->update([
            '_status' => self::FAILED,
            'failure_reason' => $reason,
            'processed_at' => now(),
        ]);
    }

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\InboundEmailMessageRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\InboundEmailMessageResource::class;
    }

    /**
     * Get the validation rules for creating a new inbound email message.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_email_messages', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'from' => 'required|array',
            'to' => 'required|array',
            'cc' => 'nullable|array',
            'bcc' => 'nullable|array',
            'subject' => 'required|string|max:255',
            'html_content' => 'nullable|string',
            'text_content' => 'nullable|string',
            'attachments' => 'nullable|array',
            'message_id' => 'required|string|unique:inbound_email_messages,message_id',
            'headers' => 'nullable|array',
            'ip_address' => 'nullable|ip',
            'mail_server' => 'nullable|string|max:255',
            'dkim_verification' => 'nullable|array',
            'spf_verification' => 'nullable|array',
            'is_spam' => 'boolean',
            'spam_score' => 'nullable|numeric|between:0,100',
            'session_id' => 'nullable|string|max:255|unique:inbound_email_messages,session_id',
            'session_amount' => 'nullable|numeric|min:0',
            'network_code' => 'nullable|string|max:100',
            'failure_reason' => 'nullable|string|max:500',
            'processed_at' => 'nullable|date',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /**
     * Get the validation rules for updating an existing inbound email message.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_email_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'from' => 'nullable|array',
            'to' => 'nullable|array',
            'cc' => 'nullable|array',
            'bcc' => 'nullable|array',
            'subject' => 'nullable|string|max:255',
            'html_content' => 'nullable|string',
            'text_content' => 'nullable|string',
            'attachments' => 'nullable|array',
            'message_id' => ['nullable', 'string', Rule::unique('inbound_email_messages', 'message_id')->ignore($id)],
            'headers' => 'nullable|array',
            'ip_address' => 'nullable|ip',
            'mail_server' => 'nullable|string|max:255',
            'dkim_verification' => 'nullable|array',
            'spf_verification' => 'nullable|array',
            'is_spam' => 'boolean',
            'spam_score' => 'nullable|numeric|between:0,100',
            'session_id' => 'nullable|string|max:255|unique:inbound_email_messages,session_id,' . $id,
            'session_amount' => 'nullable|numeric|min:0',
            'network_code' => 'nullable|string|max:100',
            'failure_reason' => 'nullable|string|max:500',
            'processed_at' => 'nullable|date',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }


}
