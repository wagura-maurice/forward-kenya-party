<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InboundTextMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inbound_text_messages';

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_FAILED = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_QUEUED = 4;
    const STATUS_SENT = 5;
    const STATUS_DELIVERED = 6;

    /**
     * Get all available status options with their labels.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_QUEUED => 'Queued',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
        ];
    }

    /**
     * Get the status value by its label.
     *
     * @param string $label
     * @return string|false
     */
    public static function getStatusValueByLabel(string $label)
    {
        $statusOptions = array_map('strtolower', self::getStatusOptions());
        $lowerLabel = strtolower($label);
        $flipped = array_flip($statusOptions);
        
        return $flipped[$lowerLabel] ?? false;
    }

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
        'content',
        'from_number',
        'to_number',
        'message_sid',
        'direction',
        'media_urls',
        'num_media',
        'session_id',
        'session_amount',
        'network_code',
        'country_code',
        'city',
        'state',
        'zip',
        'failure_reason',
        'error_code',
        'message_metadata',
        'sent_at',
        'delivered_at',
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
    protected $casts = [
        'media_urls' => 'array',
        'num_media' => 'integer',
        'session_amount' => 'decimal:2',
        'error_code' => 'integer',
        'message_metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        '_status' => 'string',
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
        'num_media' => 0,
        'session_amount' => 0,
        '_status' => self::STATUS_RECEIVED,
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\InboundTextMessageRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\InboundTextMessageResource::class;
    }

    /**
     * Get the validation rules for creating a new text message.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_text_messages', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'content' => 'required|string',
            'from_number' => 'required|string|max:20',
            'to_number' => 'required|string|max:20',
            'message_sid' => 'required|string|max:255|unique:inbound_text_messages,message_sid',
            'direction' => 'in:inbound,outbound',
            'media_urls' => 'nullable|array',
            'media_urls.*' => 'url|max:500',
            'num_media' => 'integer|min:0',
            'session_id' => 'nullable|string|max:255|unique:inbound_text_messages,session_id',
            'session_amount' => 'nullable|numeric|min:0',
            'network_code' => 'nullable|string|max:10',
            'country_code' => 'nullable|string|size:2',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'failure_reason' => 'nullable|string|max:1000',
            'error_code' => 'nullable|integer',
            'message_metadata' => 'nullable|array',
            'sent_at' => 'nullable|date',
            'delivered_at' => 'nullable|date|after_or_equal:sent_at',
            '_status' => 'required|string|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }

    /**
     * Get the validation rules for updating an existing text message.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_text_messages', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'content' => 'nullable|string',
            'from_number' => 'nullable|string|max:20',
            'to_number' => 'nullable|string|max:20',
            'message_sid' => ['nullable', 'string', 'max:255', Rule::unique('inbound_text_messages', 'message_sid')->ignore($id)],
            'direction' => 'nullable|in:inbound,outbound',
            'media_urls' => 'nullable|array',
            'media_urls.*' => 'url|max:500',
            'num_media' => 'nullable|integer|min:0',
            'session_id' => 'nullable|string|max:255|unique:inbound_text_messages,session_id,' . $id,
            'session_amount' => 'nullable|numeric|min:0',
            'network_code' => 'nullable|string|max:10',
            'country_code' => 'nullable|string|size:2',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'failure_reason' => 'nullable|string|max:1000',
            'error_code' => 'nullable|integer',
            'message_metadata' => 'nullable|array',
            'sent_at' => 'nullable|date',
            'delivered_at' => 'nullable|date|after_or_equal:sent_at',
            '_status' => 'nullable|string|in:' . implode(',', array_keys(self::getStatusOptions())),
        ];
    }
    
    /**
     * Get the communication that owns the text message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    /**
     * Get the communication type that owns the text message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }

    /**
     * Get the communication category that owns the text message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }
    
    /**
     * Scope a query to only include inbound messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }
    
    /**
     * Scope a query to only include outbound messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }
    
    /**
     * Scope a query to only include messages with media (MMS).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMedia($query)
    {
        return $query->where('num_media', '>', 0);
    }
    
    /**
     * Check if the message has any media attachments.
     *
     * @return bool
     */
    public function hasMedia(): bool
    {
        return $this->num_media > 0 && !empty($this->media_urls);
    }
    
    /**
     * Get the first media URL if available.
     *
     * @return string|null
     */
    public function getFirstMediaUrl(): ?string
    {
        return $this->media_urls[0] ?? null;
    }
}
