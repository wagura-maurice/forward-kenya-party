<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity as BaseActivity;

class Activity extends BaseActivity
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activities';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    // Status constants
    const PENDING = 0;
    const COMPLETED = 1;
    const FAILED = 2;
    const IN_PROGRESS = 3;

    public static function statusLabels()
    {
        return [
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::IN_PROGRESS => 'In Progress',
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type_id' => 1,
        'category_id' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'category_id',
        'uuid',
        'user_id',
        'service_id',
        'department_id',
        'activityable_type',
        'activityable_id',
        'related_to_id',
        'title',
        'action',
        'details',
        '_status',
        'scheduled_for',
        'started_at',
        'completed_at',
        'ip_address',
        'user_agent',
        'metadata',
        'uuid'
    ];

    protected $casts = [
        'properties' => 'collection',
        'metadata' => 'array',
        'scheduled_for' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who was affected by the activity.
     */
    public function affectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'properties->affected_user_id');
    }

    /**
     * Get the user who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the activity.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class, 'category_id');
    }

    /**
     * Get the type of the activity.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class, 'type_id');
    }

    /**
     * Get the request class for validation.
     */
    protected function getRequestClass(): string
    {
        return 'App\Http\Requests\ActivityRequest';
    }

    /**
     * Get the log name to use for the model.
     */
    public function getLogNameToUse(string $eventName = ''): string
    {
        return $this->log_name ?? config('activitylog.default_log_name', 'default');
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ActivityResource::class;
    }

    public static function createRules()
    {
        return [
            'type_id' => 'required|exists:activity_types,id',
            'category_id' => 'required|exists:activity_categories,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'metadata' => 'nullable|json',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ];
    }

    public static function updateRules()
    {
        return [
            'type_id' => 'nullable|exists:activity_types,id',
            'category_id' => 'nullable|exists:activity_categories,id',
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'metadata' => 'nullable|json',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ];
    }
    
    /**
     * Get the department associated with the activity.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    /**
     * Get the owner of the activity (polymorphic relationship).
     * This can be a Citizen, Resident, Refugee, etc.
     */
    public function activityable()
    {
        return $this->morphTo();
    }
    
    /**
     * Get all notifications for this activity.
     */
    public function notifications()
    {
        return $this->hasMany(ActivityNotification::class);
    }
    
    /**
     * Scope a query to only include activities for a specific service.
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }
    
    /**
     * Scope a query to only include activities for a specific department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
    
    /**
     * Scope a query to only include activities for a specific user type.
     */
    public function scopeForUserType($query, $userType)
    {
        return $query->where('activityable_type', $userType);
    }
    
    /**
     * Get the status label for the activity.
     */
    public function getStatusLabelAttribute()
    {
        return self::statusLabels()[$this->_status] ?? 'Unknown';
    }
    
    /**
     * Mark the activity as in progress.
     */
    public function markAsInProgress()
    {
        $this->update([
            '_status' => self::IN_PROGRESS,
            'started_at' => now(),
        ]);
    }
    
    /**
     * Mark the activity as completed.
     */
    public function markAsCompleted($details = null)
    {
        $updates = [
            '_status' => self::COMPLETED,
            'completed_at' => now(),
        ];
        
        if ($details) {
            $updates['details'] = $details;
        }
        
        $this->update($updates);
    }
    
    /**
     * Mark the activity as failed.
     */
    public function markAsFailed($error = null)
    {
        $updates = [
            '_status' => self::FAILED,
            'completed_at' => now(),
        ];
        
        if ($error) {
            $updates['details'] = $error;
        }
        
        $this->update($updates);
    }
}
