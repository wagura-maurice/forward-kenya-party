<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InboundVoiceMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inbound_voice_messages';

    // Status constants
    const PENDING = 0;
    const PROCESSED = 1;
    const FAILED = 2;

    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
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
        'call_sid',
        'recording_sid',
        'from_number',
        'to_number',
        'direction',
        'call_started_at',
        'call_ended_at',
        'duration',
        'recording_url',
        'recording_duration',
        'jitter',
        'latency',
        'packet_loss',
        'session_id',
        'session_amount',
        'network_code',
        'country_code',
        'failure_reason',
        'call_metadata',
        '_status',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['communication', 'type', 'category'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'call_started_at' => 'datetime',
        'call_ended_at' => 'datetime',
        'duration' => 'integer',
        'recording_duration' => 'integer',
        'jitter' => 'decimal:2',
        'latency' => 'decimal:2',
        'packet_loss' => 'integer',
        'session_amount' => 'decimal:2',
        'call_metadata' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'direction' => 'inbound',
        '_status' => self::PENDING,
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\InboundVoiceMessageRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\InboundVoiceMessageResource::class;
    }

    /**
     * Get the validation rules for creating a new voice message.
     *
     * @return array
     */
    /**
     * Get the validation rules for creating a new voice message.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_voice_messages', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'call_sid' => 'required|string|max:255|unique:inbound_voice_messages,call_sid',
            'recording_sid' => 'nullable|string|max:255|unique:inbound_voice_messages,recording_sid',
            'from_number' => 'required|string|max:20',
            'to_number' => 'required|string|max:20',
            'direction' => 'in:inbound,outbound',
            'call_started_at' => 'nullable|date',
            'call_ended_at' => 'nullable|date|after_or_equal:call_started_at',
            'duration' => 'nullable|integer|min:0',
            'recording_url' => 'nullable|url|max:500',
            'recording_duration' => 'nullable|integer|min:0',
            'jitter' => 'nullable|numeric|min:0',
            'latency' => 'nullable|numeric|min:0',
            'packet_loss' => 'nullable|integer|between:0,100',
            'session_id' => 'nullable|string|max:255|unique:inbound_voice_messages,session_id',
            'session_amount' => 'nullable|numeric|min:0',
            'network_code' => 'nullable|string|max:10',
            'country_code' => 'nullable|string|size:2',
            'failure_reason' => 'nullable|string|max:1000',
            'call_metadata' => 'nullable|array',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /**
     * Get the validation rules for updating an existing voice message.
     *
     * @param int $id
     * @return array
     */
    /**
     * Get the validation rules for updating an existing voice message.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_voice_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'call_sid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_voice_messages', 'call_sid')->ignore($id)],
            'recording_sid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_voice_messages', 'recording_sid')->ignore($id)],
            'from_number' => 'nullable|string|max:20',
            'to_number' => 'nullable|string|max:20',
            'direction' => 'nullable|in:inbound,outbound',
            'call_started_at' => 'nullable|date',
            'call_ended_at' => 'nullable|date|after_or_equal:call_started_at',
            'duration' => 'nullable|integer|min:0',
            'recording_url' => 'nullable|url|max:500',
            'recording_duration' => 'nullable|integer|min:0',
            'jitter' => 'nullable|numeric|min:0',
            'latency' => 'nullable|numeric|min:0',
            'packet_loss' => 'nullable|integer|between:0,100',
            'session_id' => 'nullable|string|max:255|unique:inbound_voice_messages,session_id,' . $id,
            'session_amount' => 'nullable|numeric|min:0',
            'network_code' => 'nullable|string|max:10',
            'country_code' => 'nullable|string|size:2',
            'failure_reason' => 'nullable|string|max:1000',
            'call_metadata' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }
    
    /**
     * Get the communication that owns the voice message.
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    /**
     * Get the communication type that owns the voice message.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }

    /**
     * Get the communication category that owns the voice message.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }
    
    /**
     * Scope a query to only include completed calls.
     */
    public function scopeCompleted($query)
    {
        return $query->where('_status', 'completed');
    }
    
    /**
     * Scope a query to only include failed calls.
     */
    public function scopeFailed($query)
    {
        return $query->where('_status', 'failed');
    }
    
    /**
     * Scope a query to only include calls from a specific number.
     */
    public function scopeFromNumber($query, $number)
    {
        return $query->where('from_number', $number);
    }
    
    /**
     * Scope a query to only include calls to a specific number.
     */
    public function scopeToNumber($query, $number)
    {
        return $query->where('to_number', $number);
    }
    
    /**
     * Check if the call is still active.
     */
    public function isActive(): bool
    {
        return in_array($this->_status, ['queued', 'ringing', 'in-progress']);
    }
    
    /**
     * Calculate the call duration in minutes.
     */
    public function getDurationInMinutesAttribute(): float
    {
        return $this->duration ? round($this->duration / 60, 2) : 0;
    }
    

}
