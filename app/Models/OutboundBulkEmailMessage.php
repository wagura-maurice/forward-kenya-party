<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutboundBulkEmailMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Status constants for message status
     */
    const PENDING = 0;
    const PROCESSING = 1;
    const PROCESSED = 2;
    const FAILED = 3;

    /**
     * Schedule constants for message scheduling
     */
    const DEFAULT = 0;
    const DAILY = 1;
    const WEEKLY = 2;
    const MONTHLY = 3;
    const CUSTOM = 4;
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'to_recipients' => 'array',
        'cc_recipients' => 'array',
        'bcc_recipients' => 'array',
        'reply_to' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'start_processing_at' => 'datetime',
        'end_processing_at' => 'datetime',
        'last_processed_at' => 'datetime',
        'total_recipients' => 'integer',
        'emails_sent' => 'integer',
        'emails_failed' => 'integer',
        'emails_opened' => 'integer',
        'links_clicked' => 'integer',
        '_status' => 'integer',
        'schedule' => 'integer',
    ];
    
    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        '_status' => self::PENDING,
        'schedule' => self::DEFAULT,
        'total_recipients' => 0,
        'emails_sent' => 0,
        'emails_failed' => 0,
        'emails_opened' => 0,
        'links_clicked' => 0,
    ];
    
    /**
     * Get the communication type associated with the bulk email message.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }
    
    /**
     * Get the communication category associated with the bulk email message.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }
    
    /**
     * Get the individual email messages associated with this bulk message.
     */
    public function individualEmails(): HasMany
    {
        return $this->hasMany(OutboundEmailMessage::class, 'outbound_bulk_email_message_id');
    }
    
    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::FAILED => 'Failed',
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

    public static function getScheduleOptions(): array
    {
        return [
            self::DEFAULT => 'Default',
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::CUSTOM => 'Custom',
        ];
    }

    public static function getScheduleValueByLabel(string $label)
    {
        $scheduleOptions = self::getScheduleOptions();
        $lowerLabel = strtolower($label);

        foreach ($scheduleOptions as $key => $value) {
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
        'communication_id',
        'type_id',
        'category_id',
        'subject',
        'html_content',
        'text_content',
        'to_recipients',
        'cc_recipients',
        'bcc_recipients',
        'reply_to',
        'schedule',
        'scheduled_at',
        'start_processing_at',
        'end_processing_at',
        'last_processed_at',
        'total_recipients',
        'emails_sent',
        'emails_failed',
        'emails_opened',
        'links_clicked',
        'error_message',
        'error_code',
        'metadata',
        '_status',
    ];

    /**
     * Get the validation rules for creating a new bulk email message.
     *
     * @return array<string, mixed>
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_bulk_email_messages', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'subject' => 'required|string|max:255',
            'html_content' => 'required_without:text_content|string|nullable',
            'text_content' => 'required_without:html_content|string|nullable',
            'to_recipients' => 'required|array|min:1',
            'to_recipients.*.name' => 'nullable|string|max:255',
            'to_recipients.*.email' => 'required|email',
            'cc_recipients' => 'nullable|array',
            'cc_recipients.*.name' => 'nullable|string|max:255',
            'cc_recipients.*.email' => 'required_with:cc_recipients|email',
            'bcc_recipients' => 'nullable|array',
            'bcc_recipients.*.name' => 'nullable|string|max:255',
            'bcc_recipients.*.email' => 'required_with:bcc_recipients|email',
            'reply_to' => 'nullable|array',
            'reply_to.*.name' => 'nullable|string|max:255',
            'reply_to.*.email' => 'required_with:reply_to|email',
            'schedule' => 'required|integer|in:' . implode(',', array_keys(self::getScheduleOptions())),
            'scheduled_at' => 'required_if:schedule,' . self::CUSTOM . '|date|after_or_equal:now|nullable',
            'start_processing_at' => 'nullable|date',
            'end_processing_at' => 'nullable|date|after_or_equal:start_processing_at',
            'last_processed_at' => 'nullable|date',
            'total_recipients' => 'sometimes|integer|min:0',
            'emails_sent' => 'sometimes|integer|min:0',
            'emails_failed' => 'sometimes|integer|min:0',
            'emails_opened' => 'sometimes|integer|min:0',
            'links_clicked' => 'sometimes|integer|min:0',
            'error_message' => 'nullable|string',
            'error_code' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
            '_status' => 'sometimes|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /**
     * Get the validation rules for updating an existing bulk email message.
     *
     * @param int $id
     * @return array<string, mixed>
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_bulk_email_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'subject' => 'sometimes|string|max:255',
            'html_content' => 'nullable|string|required_without:text_content',
            'text_content' => 'nullable|string|required_without:html_content',
            'to_recipients' => 'sometimes|array|min:1',
            'to_recipients.*.name' => 'nullable|string|max:255',
            'to_recipients.*.email' => 'required_with:to_recipients|email',
            'cc_recipients' => 'nullable|array',
            'cc_recipients.*.name' => 'nullable|string|max:255',
            'cc_recipients.*.email' => 'required_with:cc_recipients|email',
            'bcc_recipients' => 'nullable|array',
            'bcc_recipients.*.name' => 'nullable|string|max:255',
            'bcc_recipients.*.email' => 'required_with:bcc_recipients|email',
            'reply_to' => 'nullable|array',
            'reply_to.*.name' => 'nullable|string|max:255',
            'reply_to.*.email' => 'required_with:reply_to|email',
            'schedule' => 'nullable|integer|in:' . implode(',', array_keys(self::getScheduleOptions())),
            'scheduled_at' => 'nullable|date',
            'start_processing_at' => 'nullable|date',
            'end_processing_at' => 'nullable|date|after_or_equal:start_processing_at',
            'last_processed_at' => 'nullable|date',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /**
     * Get the communication that owns the bulk email message.
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class, 'communication_id');
    }
    
    /**
     * Scope a query to only include scheduled messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '>', now())
                    ->whereIn('_status', [self::PENDING, self::FAILED]);
    }
    
    /**
     * Mark the message as processing.
     *
     * @return bool
     */
    public function markAsProcessing(): bool
    {
        $this->_status = self::PROCESSING;
        $this->start_processing_at = $this->start_processing_at ?? now();
        $this->last_processed_at = now();
        
        return $this->save();
    }
    
    /**
     * Mark the message as processed.
     *
     * @return bool
     */
    public function markAsProcessed(): bool
    {
        $this->_status = self::PROCESSED;
        $this->end_processing_at = now();
        $this->last_processed_at = now();
        
        return $this->save();
    }
    
    /**
     * Mark the message as failed.
     *
     * @param string $errorMessage
     * @param string|null $errorCode
     * @return bool
     */
    public function markAsFailed(string $errorMessage, ?string $errorCode = null): bool
    {
        $this->_status = self::FAILED;
        $this->error_message = $errorMessage;
        $this->error_code = $errorCode;
        $this->end_processing_at = now();
        $this->last_processed_at = now();
        
        return $this->save();
    }
    
    /**
     * Increment the count of sent emails.
     *
     * @param int $count
     * @return bool
     */
    public function incrementSentCount(int $count = 1): bool
    {
        $this->emails_sent += $count;
        return $this->save();
    }
    
    /**
     * Increment the count of failed emails.
     *
     * @param int $count
     * @return bool
     */
    public function incrementFailedCount(int $count = 1): bool
    {
        $this->emails_failed += $count;
        return $this->save();
    }
    
    /**
     * Increment the count of opened emails.
     *
     * @param int $count
     * @return bool
     */
    public function incrementOpenedCount(int $count = 1): bool
    {
        $this->emails_opened += $count;
        return $this->save();
    }
    
    /**
     * Increment the count of clicked links.
     *
     * @param int $count
     * @return bool
     */
    public function incrementClickedLinksCount(int $count = 1): bool
    {
        $this->links_clicked += $count;
        return $this->save();
    }
    
    /**
     * Check if the message is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->_status === self::PENDING;
    }
    
    /**
     * Check if the message is processing.
     *
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->_status === self::PROCESSING;
    }
    
    /**
     * Check if the message is processed.
     *
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->_status === self::PROCESSED;
    }
    
    /**
     * Check if the message failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->_status === self::FAILED;
    }
    
    /**
     * Check if the message is scheduled for future sending.
     *
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture() && 
               in_array($this->_status, [self::PENDING, self::FAILED]);
    }
}
