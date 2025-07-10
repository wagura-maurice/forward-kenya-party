<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const OPEN = 1;
    const RESOLVED = 2;
    const CLOSED = 3;

    // Priority constants
    const LOW = 0;
    const MEDIUM = 1;
    const HIGH = 2;

    // Source constants
    const SOURCE_WEB = 'web';
    const SOURCE_EMAIL = 'email';
    const SOURCE_API = 'api';
    const SOURCE_MOBILE = 'mobile';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attachments' => 'array',
        'responded_at' => 'datetime',
        'is_public' => 'boolean',
        'allow_contact' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        '_status' => self::PENDING,
        '_priority' => self::MEDIUM,
        'source' => self::SOURCE_WEB,
        'vote_score' => 0,
        'is_public' => false,
        'allow_contact' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'user_id',
        'assigned_to',
        'subject',
        'message',
        'attachments',
        '_status',
        '_priority',
        'contact_name',
        'contact_email',
        'contact_phone',
        'admin_notes',
        'response',
        'responded_at',
        'responded_by',
        'source',
        'url',
        'browser',
        'device',
        'os',
        'vote_score',
        'is_public',
        'allow_contact',
        'ip_address',
        'user_agent',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_label',
        'priority_label',
        'is_anonymous',
        'has_response',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feedback) {
            if (empty($feedback->uuid)) {
                $feedback->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get the status options for feedback.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::OPEN => 'Open',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        ];
    }

    /**
     * Get the status label attribute.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->_status] ?? 'Unknown';
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
            if (strtolower($value) === $lowerLabel) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Get the priority options for feedback.
     *
     * @return array
     */
    public static function getPriorityOptions(): array
    {
        return [
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        ];
    }

    /**
     * Get the priority label attribute.
     *
     * @return string
     */
    public function getPriorityLabelAttribute(): string
    {
        return self::getPriorityOptions()[$this->_priority] ?? 'Unknown';
    }

    /**
     * Get the source options for feedback.
     *
     * @return array
     */
    public static function getSourceOptions(): array
    {
        return [
            self::SOURCE_WEB => 'Web',
            self::SOURCE_EMAIL => 'Email',
            self::SOURCE_API => 'API',
            self::SOURCE_MOBILE => 'Mobile App',
        ];
    }

    /**
     * Check if the feedback is anonymous.
     *
     * @return bool
     */
    public function getIsAnonymousAttribute(): bool
    {
        return empty($this->user_id);
    }

    /**
     * Check if the feedback has a response.
     *
     * @return bool
     */
    public function getHasResponseAttribute(): bool
    {
        return !empty($this->response) && !empty($this->responded_at);
    }

    /**
     * Get the validation rules for creating a new feedback.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'type_id' => ['required', 'exists:feedback_types,id'],
            'category_id' => ['required', 'exists:feedback_categories,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:5120'], // 5MB max per file
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'allow_contact' => ['boolean'],
            'is_public' => ['boolean'],
            'source' => ['nullable', 'string', Rule::in(array_keys(self::getSourceOptions()))],
        ];
    }

    /**
     * Get the validation rules for updating an existing feedback.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules($id): array
    {
        return [
            'type_id' => ['nullable', 'exists:feedback_types,id'],
            'category_id' => ['nullable', 'exists:feedback_categories,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
            'response' => ['nullable', 'string'],
            '_status' => ['nullable', 'integer', Rule::in(array_keys(self::getStatusOptions()))],
            '_priority' => ['nullable', 'integer', Rule::in(array_keys(self::getPriorityOptions()))],
            'is_public' => ['boolean'],
        ];
    }

    /**
     * Scope a query to only include public feedback.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include feedback by status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('_status', $status);
    }

    /**
     * Get the feedback type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeedbackType::class, 'type_id');
    }

    /**
     * Get the feedback category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeedbackCategory::class, 'category_id');
    }

    /**
     * Get the user who submitted the feedback.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the staff member assigned to handle the feedback.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the admin who responded to the feedback.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Record a response to the feedback.
     *
     * @param string $response
     * @param int $userId
     * @return bool
     */
    public function respond(string $response, int $userId): bool
    {
        $this->response = $response;
        $this->responded_by = $userId;
        $this->responded_at = now();
        $this->_status = self::RESOLVED;
        
        return $this->save();
    }

    /**
     * Upvote the feedback.
     *
     * @param int $increment
     * @return bool
     */
    public function upvote(int $increment = 1): bool
    {
        return $this->increment('vote_score', $increment) > 0;
    }

    /**
     * Downvote the feedback.
     *
     * @param int $decrement
     * @return bool
     */
    public function downvote(int $decrement = 1): bool
    {
        return $this->decrement('vote_score', $decrement) > 0;
    }
}
