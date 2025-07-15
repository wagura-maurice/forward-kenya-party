<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\Authorizable;

class ActivityNotification extends Model
{
    use SoftDeletes, Authorizable;

    // Status constants
    public const PENDING = 0;
    public const SENT = 1;
    public const FAILED = 2;
    public const READ = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'activity_id',
        'user_id',
        'title',
        'message',
        'data',
        'read_at',
        'sent_at',
        'failed_at',
        'error_message',
        'metadata',
        '_status',
    ];
    
    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'is_read',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        '_status' => 'integer',
    ];

    /**
     * Get the validation rules for creating/updating.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'activity_id' => 'required|exists:activities,id',
            'user_id' => 'required|exists:users,id',
            'data' => 'nullable|array',
            'metadata' => 'nullable|array',
            '_status' => 'sometimes|integer|in:' . implode(',', [
                self::PENDING,
                self::SENT,
                self::FAILED,
                self::READ,
            ]),
        ];
    }

    /**
     * Get the activity that the notification belongs to.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the user that the notification is for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Scope a query to only include notifications for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    
    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }
    
    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update([
                'read_at' => now(),
                '_status' => self::READ,
            ]);
        }
    }
    
    /**
     * Mark the notification as unread.
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->update([
                'read_at' => null,
                '_status' => self::PENDING,
            ]);
        }
    }
    
    /**
     * Mark the notification as sent.
     */
    public function markAsSent()
    {
        $this->update([
            'sent_at' => now(),
            '_status' => self::SENT,
        ]);
    }
    
    /**
     * Mark the notification as failed.
     */
    public function markAsFailed($error = null)
    {
        $updates = [
            'failed_at' => now(),
            '_status' => self::FAILED,
        ];
        
        if ($error) {
            $updates['error_message'] = $error;
        }
        
        $this->update($updates);
    }
    
    /**
     * Get the status label for the notification.
     */
    public function getStatusLabelAttribute()
    {
        return [
            self::PENDING => 'Pending',
            self::SENT => 'Sent',
            self::FAILED => 'Failed',
            self::READ => 'Read',
        ][$this->_status] ?? 'Unknown';
    }
    
    /**
     * Check if the notification has been read.
     */
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }
    
}
