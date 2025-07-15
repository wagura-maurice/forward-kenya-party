<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const OPEN = 0;
    const IN_PROGRESS = 1;
    const RESOLVED = 2;
    const CLOSED = 3;

    // Priority constants
    const LOW = 0;
    const MEDIUM = 1;
    const HIGH = 2;

    // Source constants
    const SOURCE_WEB = 0;
    const SOURCE_EMAIL = 1;
    const SOURCE_API = 2;
    const SOURCE_PHONE = 3;
    const SOURCE_CHAT = 4;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'custom_fields' => 'array',
        'tags' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        '_status' => self::OPEN,
        '_priority' => self::MEDIUM,
        'source' => self::SOURCE_WEB,
        'reopen_count' => 0,
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
        'title',
        'ticket_number',
        'description',
        '_status',
        '_priority',
        'due_date',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'response_time',
        'resolution_time',
        'reopen_count',
        'source',
        'custom_fields',
        'tags',
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
        'is_overdue',
        'is_escalated',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
            if (empty($ticket->uuid)) {
                $ticket->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::updating(function ($ticket) {
            // Update timestamps when status changes
            if ($ticket->isDirty('_status')) {
                $now = now();
                
                if ($ticket->_status === self::RESOLVED && !$ticket->resolved_at) {
                    $ticket->resolved_at = $now;
                    $ticket->resolution_time = $ticket->created_at->diffInMinutes($now);
                }
                
                if ($ticket->_status === self::CLOSED && !$ticket->closed_at) {
                    $ticket->closed_at = $now;
                }
                
                if ($ticket->_status === self::IN_PROGRESS && !$ticket->first_response_at) {
                    $ticket->first_response_at = $now;
                    $ticket->response_time = $ticket->created_at->diffInMinutes($now);
                }
            }
        });
    }

    /**
     * Generate a unique ticket number.
     *
     * @return string
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT-' . now()->format('Ymd') . '-';
        $lastTicket = static::where('ticket_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = 1;
        if ($lastTicket) {
            $lastNumber = (int) str_replace($prefix, '', $lastTicket->ticket_number);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the status options for tickets.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
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
     * Get the priority options for tickets.
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
     * Get the priority value by label.
     *
     * @param string $label
     * @return int|false
     */
    public static function getPriorityValueByLabel(string $label)
    {
        $priorityOptions = self::getPriorityOptions();
        $lowerLabel = strtolower($label);

        foreach ($priorityOptions as $key => $value) {
            if (strtolower($value) === $lowerLabel) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Get the available ticket sources.
     *
     * @return array
     */
    public static function getSourceOptions(): array
    {
        return [
            self::SOURCE_WEB => 'Web',
            self::SOURCE_EMAIL => 'Email',
            self::SOURCE_API => 'API',
            self::SOURCE_PHONE => 'Phone',
            self::SOURCE_CHAT => 'Chat',
        ];
    }

    /**
     * Check if the ticket is overdue.
     *
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isPast() && 
               in_array($this->_status, [self::OPEN, self::IN_PROGRESS]);
    }

    /**
     * Check if the ticket is escalated.
     *
     * @return bool
     */
    public function getIsEscalatedAttribute(): bool
    {
        // Example: Consider a ticket escalated if it's high priority and open for more than 24 hours
        return $this->_priority === self::HIGH && 
               $this->created_at->diffInHours(now()) > 24 &&
               in_array($this->_status, [self::OPEN, self::IN_PROGRESS]);
    }

    /**
     * Get the validation rules for creating a new ticket.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'type_id' => ['required', 'exists:ticket_types,id'],
            'category_id' => ['required', 'exists:ticket_categories,id'],
            'user_id' => ['required', 'exists:users,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            '_status' => ['nullable', 'integer', Rule::in(array_keys(self::getStatusOptions()))],
            '_priority' => ['nullable', 'integer', Rule::in(array_keys(self::getPriorityOptions()))],
            'due_date' => ['nullable', 'date', 'after:now'],
            'source' => ['nullable', 'string', Rule::in(array_keys(self::getSourceOptions()))],
            'tags' => ['nullable', 'array'],
            'custom_fields' => ['nullable', 'array'],
        ];
    }

    /**
     * Get the validation rules for updating an existing ticket.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules($id): array
    {
        return [
            'type_id' => ['nullable', 'exists:ticket_types,id'],
            'category_id' => ['nullable', 'exists:ticket_categories,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            '_status' => ['nullable', 'integer', Rule::in(array_keys(self::getStatusOptions()))],
            '_priority' => ['nullable', 'integer', Rule::in(array_keys(self::getPriorityOptions()))],
            'due_date' => ['nullable', 'date'],
            'resolved_at' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date'],
            'source' => ['nullable', 'string', Rule::in(array_keys(self::getSourceOptions()))],
            'tags' => ['nullable', 'array'],
            'custom_fields' => ['nullable', 'array'],
        ];
    }

    /**
     * Scope a query to only include open tickets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('_status', self::OPEN);
    }

    /**
     * Scope a query to only include tickets assigned to a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope a query to only include overdue tickets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('_status', [self::OPEN, self::IN_PROGRESS]);
    }

    /**
     * Scope a query to only include tickets with high priority.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHighPriority($query)
    {
        return $query->where('_priority', self::HIGH);
    }

    /**
     * Get the ticket type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'type_id');
    }

    /**
     * Get the ticket category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Get the user who created the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user assigned to the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all of the ticket's comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    /**
     * Get all of the ticket's attachments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Get all of the ticket's history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(TicketHistory::class)->latest();
    }

    /**
     * Mark the ticket as in progress.
     *
     * @return bool
     */
    public function markAsInProgress(): bool
    {
        if ($this->_status !== self::IN_PROGRESS) {
            $this->_status = self::IN_PROGRESS;
            $this->first_response_at = $this->first_response_at ?? now();
            return $this->save();
        }
        
        return false;
    }

    /**
     * Mark the ticket as resolved.
     *
     * @return bool
     */
    public function markAsResolved(): bool
    {
        if ($this->_status !== self::RESOLVED) {
            $this->_status = self::RESOLVED;
            $this->resolved_at = now();
            $this->resolution_time = $this->created_at->diffInMinutes(now());
            return $this->save();
        }
        
        return false;
    }

    /**
     * Mark the ticket as closed.
     *
     * @return bool
     */
    public function markAsClosed(): bool
    {
        if ($this->_status !== self::CLOSED) {
            $this->_status = self::CLOSED;
            $this->closed_at = now();
            return $this->save();
        }
        
        return false;
    }

    /**
     * Reopen the ticket.
     *
     * @return bool
     */
    public function reopen(): bool
    {
        if (in_array($this->_status, [self::RESOLVED, self::CLOSED])) {
            $this->_status = self::OPEN;
            $this->reopen_count++;
            $this->resolved_at = null;
            $this->closed_at = null;
            return $this->save();
        }
        
        return false;
    }
}
