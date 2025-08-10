<?php

namespace App\Traits;

use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as BaseLogsActivity;

trait LogsActivityWithMetadata
{
    use BaseLogsActivity;

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        $options = $this->getDefaultLogOptions();
        
        // Add IP and user agent to properties
        $options->setDescriptionForEvent(function(string $eventName) {
            $description = $this->getDefaultDescription($eventName);
            
            // Add IP and user agent to properties
            $properties = [
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ];
            
            // Merge with existing properties if any
            if (method_exists($this, 'getActivitylogProperties')) {
                $properties = array_merge($properties, $this->getActivitylogProperties($eventName) ?? []);
            }
            
            // Store properties in the activity
            $this->activitylogProperties = $properties;
            
            return $description;
        });
        
        return $options;
    }
    
    /**
     * Get the default log options for the model.
     */
    protected function getDefaultLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept([
                'created_at',
                'updated_at',
                'deleted_at',
                'remember_token',
                'two_factor_recovery_codes',
                'two_factor_secret',
                'email_verified_at',
            ]);
    }
    
    /**
     * Get the default description for the activity.
     */
    protected function getDefaultDescription(string $eventName): string
    {
        $modelName = class_basename($this);
        $modelId = $this->id ?? 'unknown';
        
        return match($eventName) {
            'created' => "{$modelName} #{$modelId} was created",
            'updated' => "{$modelName} #{$modelId} was updated",
            'deleted' => "{$modelName} #{$modelId} was deleted",
            'restored' => "{$modelName} #{$modelId} was restored",
            'forceDeleted' => "{$modelName} #{$modelId} was permanently deleted",
            default => "{$modelName} #{$modelId} {$eventName}",
        };
    }
    
    /**
     * Tap the activity before it gets saved.
     */
    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName)
    {
        // Set IP address and user agent directly on the activity
        $activity->ip_address = Request::ip();
        $activity->user_agent = Request::userAgent();
        
        // Set the causer if not already set
        if (auth()->check() && !$activity->causer_id) {
            $activity->causer_type = get_class(auth()->user());
            $activity->causer_id = auth()->id();
        }
    }
}
