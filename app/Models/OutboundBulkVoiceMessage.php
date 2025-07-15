<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Media;

class OutboundBulkVoiceMessage extends Model
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
     * @var array
     */
    protected $casts = [
        'recipients' => 'array',
        'variables' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'start_processing_at' => 'datetime',
        'end_processing_at' => 'datetime',
        'last_processed_at' => 'datetime',
    ];
    
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        '_status' => self::PENDING,
        'schedule' => self::DEFAULT,
        'language' => 'en',
        'voice' => 'en-US-Standard-A',
        'total_recipients' => 0,
        'calls_initiated' => 0,
        'calls_answered' => 0,
        'calls_completed' => 0,
        'calls_failed' => 0,
        'duration_total' => 0,
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
        'media_id',
        'message',
        'language',
        'voice',
        'recipients',
        'variables',
        'schedule',
        'scheduled_at',
        'start_processing_at',
        'end_processing_at',
        'last_processed_at',
        'total_recipients',
        'calls_initiated',
        'calls_answered',
        'calls_completed',
        'calls_failed',
        'duration_total',
        'error_message',
        'error_code',
        'metadata',
        '_status',
    ];

    /**
     * Get the status options for the message.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::FAILED => 'Failed',
        ];
    }

    /**
     * Get the validation rules for creating a new bulk voice message.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_bulk_voice_messages', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'media_id' => 'nullable|exists:media,id',
            'message' => 'required_without:media_id|string|nullable',
            'language' => 'nullable|string|max:10',
            'voice' => 'nullable|string|max:50',
            'recipients' => 'required|array|min:1',
            'recipients.*.phone' => 'required|string',
            'recipients.*.name' => 'nullable|string|max:255',
            'variables' => 'nullable|array',
            'schedule' => 'required|integer|in:' . implode(',', array_keys(self::getScheduleOptions())),
            'scheduled_at' => 'required_if:schedule,' . self::CUSTOM . '|date|after_or_equal:now|nullable',
            'start_processing_at' => 'nullable|date',
            'end_processing_at' => 'nullable|date|after_or_equal:start_processing_at',
            'last_processed_at' => 'nullable|date',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /**
     * Get the validation rules for updating an existing bulk voice message.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_bulk_voice_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'media_id' => 'nullable|exists:media,id',
            'message' => 'nullable|string|required_without:media_id',
            'language' => 'nullable|string|max:10',
            'voice' => 'nullable|string|max:50',
            'recipients' => 'nullable|array|min:1',
            'recipients.*.phone' => 'required_with:recipients|string',
            'recipients.*.name' => 'nullable|string|max:255',
            'variables' => 'nullable|array',
            'schedule' => 'nullable|integer|in:' . implode(',', array_keys(self::getScheduleOptions())),
            'scheduled_at' => 'nullable|date|required_if:schedule,' . self::CUSTOM,
            'start_processing_at' => 'nullable|date',
            'end_processing_at' => 'nullable|date|after_or_equal:start_processing_at',
            'last_processed_at' => 'nullable|date',
            'metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
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
     * Get the schedule options for the message.
     *
     * @return array
     */
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

    /**
     * Get the communication that owns the bulk voice message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class, 'communication_id');
    }

    /**
     * Get the media that owns the bulk voice message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
    
    /**
     * Get the communication type associated with the bulk voice message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }
    
    /**
     * Get the communication category associated with the bulk voice message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }
    
    /**
     * Scope a query to only include pending messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('_status', self::PENDING);
    }
    
    /**
     * Scope a query to only include processing messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing($query)
    {
        return $query->where('_status', self::PROCESSING);
    }
    
    /**
     * Scope a query to only include processed messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessed($query)
    {
        return $query->where('_status', self::PROCESSED);
    }
    
    /**
     * Scope a query to only include failed messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('_status', self::FAILED);
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
     * Increment call counters.
     *
     * @param bool $answered
     * @param bool $completed
     * @param int $duration
     * @return bool
     */
    public function incrementCallCounters(bool $answered, bool $completed, int $duration = 0): bool
    {
        $this->calls_initiated++;
        
        if ($answered) {
            $this->calls_answered++;
            $this->duration_total += $duration;
            
            if ($completed) {
                $this->calls_completed++;
            } else {
                $this->calls_failed++;
            }
        } else {
            $this->calls_failed++;
        }
        
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
}
