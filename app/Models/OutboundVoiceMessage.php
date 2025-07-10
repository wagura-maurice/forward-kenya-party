<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutboundVoiceMessage extends Model
{
    use HasFactory, SoftDeletes;

    // Call statuses
    const STATUS_PENDING = 0;
    const STATUS_QUEUED = 1;
    const STATUS_RINGING = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_BUSY = 5;
    const STATUS_FAILED = 6;
    const STATUS_NO_ANSWER = 7;
    const STATUS_CANCELED = 8;
    
    // Answered by
    const ANSWERED_HUMAN = 0;
    const ANSWERED_MACHINE = 1;
    const ANSWERED_FAX = 2;
    const ANSWERED_OTHER = 3;

    /**
     * Get all available call status options.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_QUEUED => 'Queued',
            self::STATUS_RINGING => 'Ringing',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_BUSY => 'Busy',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_NO_ANSWER => 'No Answer',
            self::STATUS_CANCELED => 'Canceled',
        ];
    }
    
    /**
     * Get all available call statuses
     */
    public static function getCallStatuses(): array
    {
        return [
            self::STATUS_QUEUED => 'Queued',
            self::STATUS_RINGING => 'Ringing',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_BUSY => 'Busy',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_NO_ANSWER => 'No Answer',
            self::STATUS_CANCELED => 'Canceled',
        ];
    }
    
    /**
     * Get all possible values for answered_by field
     */
    public static function getAnsweredByOptions(): array
    {
        return [
            self::ANSWERED_HUMAN => 'Human',
            self::ANSWERED_MACHINE => 'Answering Machine',
            self::ANSWERED_FAX => 'Fax Machine',
            self::ANSWERED_OTHER => 'Other',
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
    protected $fillable = [
        'uuid',
        'communication_id',
        'type_id',
        'category_id',
        'outbound_bulk_text_message_id',
        'media_id',
        'call_sid',
        'from_number',
        'to_number',
        'direction',
        'call_status',      // String status (e.g., 'queued', 'in-progress')
        'answered_by',
        'queued_at',
        'initiated_at',
        'answered_at',
        'ended_at',
        'duration',
        'price',
        'price_unit',
        'ring_duration',
        'recording_sid',
        'recording_url',
        'recording_duration',
        'jitter',
        'latency',
        'packet_loss',
        'session_id',
        'session_amount',
        'failure_reason',
        'error_code',
        'caller_name',
        'forwarded_from',
        'caller_country',
        'called_country',
        'caller_state',
        'called_state',
        'caller_city',
        'called_city',
        'caller_zip',
        'called_zip',
        '_status',          // Integer processing status (e.g., 0 = PENDING, 1 = PROCESSING, etc.)
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'queued_at' => 'datetime',
        'initiated_at' => 'datetime',
        'answered_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'price' => 'decimal:5',
        'ring_duration' => 'integer',
        'recording_duration' => 'integer',
        'jitter' => 'decimal:2',
        'latency' => 'decimal:2',
        'packet_loss' => 'decimal:2',
        'session_amount' => 'decimal:2',
        '_status' => 'integer',  // Processing status (integer)
        'call_status' => 'string', // Call status (string)
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['communication', 'type', 'category'];

    protected $attributes = [
        'call_status' => self::STATUS_QUEUED,  // String status
        'direction' => 'outbound-api',
        'duration' => 0,
        'session_amount' => 0,
        '_status' => self::STATUS_PENDING,            // Integer status
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\OutboundVoiceMessageRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\OutboundVoiceMessageResource::class;
    }

    /**
     * Get the validation rules for creating a new outbound voice message.
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_voice_messages', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'outbound_bulk_text_message_id' => 'nullable|exists:outbound_bulk_text_messages,id',
            'media_id' => 'required|exists:media,id',
            'call_sid' => 'required|string|unique:outbound_voice_messages,call_sid',
            'from_number' => 'required|string|max:50',
            'to_number' => 'required|string|max:50',
            'direction' => 'sometimes|string|in:outbound-api,outbound-dial',
            'call_status' => 'sometimes|string|in:' . implode(',', [
                self::STATUS_QUEUED,
                self::STATUS_RINGING,
                self::STATUS_IN_PROGRESS,
                self::STATUS_COMPLETED,
                self::STATUS_BUSY,
                self::STATUS_FAILED,
                self::STATUS_NO_ANSWER,
                self::STATUS_CANCELED,
            ]),
            '_status' => 'sometimes|integer|in:' . implode(',', [
                self::STATUS_PENDING,
                self::STATUS_QUEUED,
                self::STATUS_RINGING,
                self::STATUS_IN_PROGRESS,
                self::STATUS_COMPLETED,
                self::STATUS_BUSY,
                self::STATUS_FAILED,
                self::STATUS_NO_ANSWER,
                self::STATUS_CANCELED,
            ]),
            'answered_by' => 'nullable|string|in:' . implode(',', array_keys(self::getAnsweredByOptions())),
            'queued_at' => 'nullable|date',
            'initiated_at' => 'nullable|date',
            'answered_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|size:3',
            'ring_duration' => 'nullable|integer|min:0',
            'recording_sid' => 'nullable|string',
            'recording_url' => 'nullable|url',
            'recording_duration' => 'nullable|integer|min:0',
            'jitter' => 'nullable|numeric|min:0',
            'latency' => 'nullable|numeric|min:0',
            'packet_loss' => 'nullable|numeric|min:0|max:100',
            'session_id' => 'nullable|string|unique:outbound_voice_messages,session_id',
            'session_amount' => 'nullable|numeric|min:0',
            'failure_reason' => 'nullable|string',
            'error_code' => 'nullable|integer',
            'caller_name' => 'nullable|string|max:255',
            'forwarded_from' => 'nullable|string|max:50',
            'caller_country' => 'nullable|string|size:2',
            'called_country' => 'nullable|string|size:2',
            'caller_state' => 'nullable|string|max:100',
            'called_state' => 'nullable|string|max:100',
            'caller_city' => 'nullable|string|max:100',
            'called_city' => 'nullable|string|max:100',
            'caller_zip' => 'nullable|string|max:20',
            'called_zip' => 'nullable|string|max:20',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /**
     * Get the validation rules for updating an outbound voice message.
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('outbound_voice_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'outbound_bulk_text_message_id' => 'nullable|exists:outbound_bulk_text_messages,id',
            'media_id' => 'sometimes|exists:media,id',
            'call_sid' => 'sometimes|string|unique:outbound_voice_messages,call_sid,' . $id,
            'from_number' => 'sometimes|string|max:50',
            'to_number' => 'sometimes|string|max:50',
            'direction' => 'sometimes|string|in:outbound-api,outbound-dial',
            'call_status' => 'sometimes|string|in:' . implode(',', [
                self::STATUS_QUEUED,
                self::STATUS_RINGING,
                self::STATUS_IN_PROGRESS,
                self::STATUS_COMPLETED,
                self::STATUS_BUSY,
                self::STATUS_FAILED,
                self::STATUS_NO_ANSWER,
                self::STATUS_CANCELED,
            ]),
            '_status' => 'sometimes|integer|in:' . implode(',', [
                self::STATUS_PENDING,
                self::STATUS_QUEUED,
                self::STATUS_RINGING,
                self::STATUS_IN_PROGRESS,
                self::STATUS_COMPLETED,
                self::STATUS_BUSY,
                self::STATUS_FAILED,
                self::STATUS_NO_ANSWER,
                self::STATUS_CANCELED,
            ]),
            'answered_by' => 'nullable|string|in:' . implode(',', array_keys(self::getAnsweredByOptions())),
            'queued_at' => 'nullable|date',
            'initiated_at' => 'nullable|date',
            'answered_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|size:3',
            'ring_duration' => 'nullable|integer|min:0',
            'recording_sid' => 'nullable|string',
            'recording_url' => 'nullable|url',
            'recording_duration' => 'nullable|integer|min:0',
            'jitter' => 'nullable|numeric|min:0',
            'latency' => 'nullable|numeric|min:0',
            'packet_loss' => 'nullable|numeric|min:0|max:100',
            'session_id' => 'nullable|string|unique:outbound_voice_messages,session_id,' . $id,
            'session_amount' => 'nullable|numeric|min:0',
            'failure_reason' => 'nullable|string',
            'error_code' => 'nullable|integer',
            'caller_name' => 'nullable|string|max:255',
            'forwarded_from' => 'nullable|string|max:50',
            'caller_country' => 'nullable|string|size:2',
            'called_country' => 'nullable|string|size:2',
            'caller_state' => 'nullable|string|max:100',
            'called_state' => 'nullable|string|max:100',
            'caller_city' => 'nullable|string|max:100',
            'called_city' => 'nullable|string|max:100',
            'caller_zip' => 'nullable|string|max:20',
            'called_zip' => 'nullable|string|max:20',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }
    
    /**
     * Get the communication that owns this voice message.
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    /**
     * Get the bulk text message associated with this voice message.
     */
    public function outboundBulkTextMessage(): BelongsTo
    {
        return $this->belongsTo(OutboundBulkTextMessage::class);
    }

    /**
     * Get the media associated with this voice message.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Get the communication type that owns the outbound voice message.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }

    /**
     * Get the communication category that owns the outbound voice message.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }
    
    /**
     * Scope a query to only include completed calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('call_status', self::STATUS_COMPLETED)
                    ->where('_status', self::STATUS_COMPLETED);
    }
    
    /**
     * Scope a query to only include failed calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('call_status', [
                self::STATUS_FAILED, 
                self::STATUS_BUSY, 
                self::STATUS_NO_ANSWER,
                self::STATUS_CANCELED
            ])
            ->orWhere('_status', self::STATUS_FAILED);
    }
    
    /**
     * Scope a query to only include active calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereIn('call_status', [
                self::STATUS_QUEUED, 
                self::STATUS_RINGING, 
                self::STATUS_IN_PROGRESS
            ])
            ->orWhere('_status', self::STATUS_QUEUED);
    }
    
    /**
     * Scope a query to only include pending calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('call_status', self::STATUS_QUEUED)
                    ->where('_status', self::STATUS_PENDING);
    }
    
    /**
     * Scope a query to only include processing calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing($query)
    {
        return $query->whereIn('call_status', [
                self::STATUS_RINGING, 
                self::STATUS_IN_PROGRESS
            ])
            ->where('_status', self::STATUS_QUEUED);
    }
    
    /**
     * Check if the call was answered.
     *
     * @return bool
     */
    public function wasAnswered(): bool
    {
        return !empty($this->answered_at) && $this->call_status === self::STATUS_COMPLETED;
    }
    
    /**
     * Check if the call is in progress.
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->_status === self::STATUS_QUEUED || 
               $this->call_status === self::STATUS_IN_PROGRESS ||
               $this->call_status === self::STATUS_RINGING;
    }
    
    /**
     * Check if the call is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->_status === self::STATUS_PENDING || 
               $this->call_status === self::STATUS_QUEUED;
    }
    
    /**
     * Check if the call was successful.
     *
     * @return bool
     */
    public function wasSuccessful(): bool
    {
        return $this->call_status === self::STATUS_COMPLETED && 
               empty($this->failure_reason) && 
               $this->_status === self::STATUS_QUEUED;
    }
    

    
    /**
     * Mark the call as processing.
     *
     * @return bool
     */
    public function markAsProcessing(): bool
    {
        $this->_status = self::STATUS_QUEUED;
        $this->call_status = self::STATUS_IN_PROGRESS;
        return $this->save();
    }
    
    /**
     * Mark the call as processed.
     *
     * @return bool
     */
    public function markAsProcessed(): bool
    {
        $this->_status = self::STATUS_QUEUED;
        return $this->save();
    }
    
    /**
     * Mark the call as completed.
     *
     * @param int|null $duration
     * @return bool
     */
    public function markAsCompleted(int $duration = null): bool
    {
        $this->call_status = self::STATUS_COMPLETED;
        $this->_status = self::STATUS_QUEUED;
        $this->ended_at = $this->ended_at ?? now();
        
        if ($duration !== null) {
            $this->duration = $duration;
        } elseif ($this->answered_at && !$this->duration) {
            $this->duration = $this->ended_at->diffInSeconds($this->answered_at);
        }
        
        return $this->save();
    }
    
    /**
     * Mark the call as failed.
     *
     * @param string|null $reason
     * @param int|null $errorCode
     * @return bool
     */
    public function markAsFailed(string $reason = null, int $errorCode = null): bool
    {
        $this->call_status = self::STATUS_FAILED;
        $this->_status = self::STATUS_FAILED;
        $this->ended_at = $this->ended_at ?? now();
        $this->failure_reason = $reason;
        $this->error_code = $errorCode;
        
        return $this->save();
    }
    
    /**
     * Mark the call as no answer.
     *
     * @return bool
     */
    public function markAsNoAnswer(): bool
    {
        $this->call_status = self::STATUS_NO_ANSWER;
        $this->_status = self::STATUS_QUEUED;
        $this->ended_at = $this->ended_at ?? now();
        $this->failure_reason = 'No answer';
        
        return $this->save();
    }
    
    /**
     * Mark the call as busy.
     *
     * @return bool
     */
    public function markAsBusy(): bool
    {
        $this->call_status = self::STATUS_BUSY;
        $this->_status = self::STATUS_QUEUED;
        $this->ended_at = $this->ended_at ?? now();
        $this->failure_reason = 'Line busy';
        
        return $this->save();
    }
    
    /**
     * Get the call duration in a human-readable format.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return '0:00';
        }
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
    
    /**
     * Get the call status in a human-readable format.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = self::getCallStatuses();
        return $statuses[$this->call_status] ?? ucfirst(str_replace('-', ' ', $this->call_status));
    }
}
