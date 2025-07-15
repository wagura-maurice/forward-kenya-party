<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Communication extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const SENT = 1;
    const FAILED = 2;

    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::SENT => 'Sent',
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
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'messageable_id',
        'messageable_type',
        '_status',
        'content',
        'recipient',
        'subject',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\CommunicationRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\CommunicationResource::class;
    }

    public static function createRules(): array
    {
        return [
            'uuid' => ['required', 'uuid', Rule::unique('communications', 'uuid')],
            'type_id' => 'required|integer|exists:communication_types,id',
            'category_id' => 'required|integer|exists:communication_categories,id',
            'messageable_id' => 'nullable|integer',
            'messageable_type' => 'nullable|string|max:255',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
            'content' => 'nullable|string',
            'recipient' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ];
    }

    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('communications', 'uuid')->ignore($id)],
            'type_id' => 'nullable|integer|exists:communication_types,id',
            'category_id' => 'nullable|integer|exists:communication_categories,id',
            'messageable_id' => 'nullable|integer',
            'messageable_type' => 'nullable|string|max:255',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
            'content' => 'nullable|string',
            'recipient' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
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
     * Get the parent messageable model (OutboundTextMessage, OutboundEmailMessage, etc.).
     */
    public function messageable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include communications of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('messageable_type', $type);
    }

    /**
     * Get the display name of the status.
     *
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return self::getStatusOptions()[$this->_status] ?? 'Unknown';
    }

    /**
     * Get the recipient name if available in metadata.
     *
     * @return string|null
     */
    public function getRecipientNameAttribute(): ?string
    {
        return $this->metadata['recipient_name'] ?? null;
    }
}
