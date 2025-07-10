<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'user_id',
        'service_id',
        'department_id',
        'activityable_id',
        'activityable_type',
        'title',
        'action',
        'description',
        'details',
        '_status',
        'scheduled_for',
        'started_at',
        'completed_at',
        'ip_address',
        'user_agent',
        'metadata'
    ];
    
    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'scheduled_for' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\ActivityRequest::class;
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

    public function type()
    {
        return $this->belongsTo(ActivityType::class, 'type_id');
    }
    
    public function category()
    {
        return $this->belongsTo(ActivityCategory::class, 'category_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the service associated with the activity.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
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
