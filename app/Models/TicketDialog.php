<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TicketDialog extends Model
{
    use SoftDeletes;

    // Dialog Types
    const TYPE_PUBLIC_NOTE = 0;
    const TYPE_PRIVATE_NOTE = 1;
    const TYPE_CUSTOMER_REPLY = 2;
    const TYPE_AGENT_REPLY = 3;
    const TYPE_SYSTEM = 4;
    const TYPE_FORWARD = 5;

    // Status Constants
    const STATUS_ACTIVE = 0;
    const STATUS_DELETED = 1;
    const STATUS_ARCHIVED = 2;
    const STATUS_PENDING_REVIEW = 3;
    const STATUS_SPAM = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'ticket_id',
        'user_id',
        'content',
        'attachments',
        'dialog_type',
        '_status',
        'ip_address',
        'user_agent',
        'internal_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attachments' => 'array',
        'dialog_type' => 'integer',
        '_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'dialog_type' => self::TYPE_PUBLIC_NOTE,
        '_status' => self::STATUS_ACTIVE,
    ];

    /**
     * Get the ticket that owns the dialog.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that created the dialog.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the dialog's activities.
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Scope a query to only include active dialogs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('_status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include public dialogs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->whereIn('dialog_type', [
            self::TYPE_PUBLIC_NOTE,
            self::TYPE_CUSTOMER_REPLY,
            self::TYPE_AGENT_REPLY,
            self::TYPE_FORWARD
        ]);
    }

    /**
     * Check if the dialog is public.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return in_array($this->dialog_type, [
            self::TYPE_PUBLIC_NOTE,
            self::TYPE_CUSTOMER_REPLY,
            self::TYPE_AGENT_REPLY,
            self::TYPE_FORWARD
        ]);
    }

    /**
     * Check if the dialog is a note.
     *
     * @return bool
     */
    public function isNote(): bool
    {
        return in_array($this->dialog_type, [
            self::TYPE_PUBLIC_NOTE,
            self::TYPE_PRIVATE_NOTE
        ]);
    }

    /**
     * Check if the dialog is a reply.
     *
     * @return bool
     */
    public function isReply(): bool
    {
        return in_array($this->dialog_type, [
            self::TYPE_CUSTOMER_REPLY,
            self::TYPE_AGENT_REPLY
        ]);
    }

    /**
     * Get the dialog type as a string.
     *
     * @return string
     */
    public function getDialogTypeNameAttribute(): string
    {
        return [
            self::TYPE_PUBLIC_NOTE => 'Public Note',
            self::TYPE_PRIVATE_NOTE => 'Private Note',
            self::TYPE_CUSTOMER_REPLY => 'Customer Reply',
            self::TYPE_AGENT_REPLY => 'Agent Reply',
            self::TYPE_SYSTEM => 'System Message',
            self::TYPE_FORWARD => 'Forwarded Message'
        ][$this->dialog_type] ?? 'Unknown';
    }

    /**
     * Get the status as a string.
     *
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_DELETED => 'Deleted',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_PENDING_REVIEW => 'Pending Review',
            self::STATUS_SPAM => 'Marked as Spam'
        ][$this->_status] ?? 'Unknown';
    }
}
