<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\CustomActivity;

/**
 * This trait provides custom activity logging functionality
 * that works with our custom activities table schema.
 * It does not use Spatie's default logging mechanism.
 */
trait LogsActivityWithMetadata
{
    
    /**
     * Boot the trait.
     */
    public static function bootLogsActivityWithMetadata()
    {
        $className = static::class;
        \Illuminate\Support\Facades\Log::info("Booting LogsActivityWithMetadata trait for {$className}");
        
        // Register event listeners with more detailed logging
        $events = ['created', 'updated', 'deleted', 'restored'];
        
        foreach ($events as $event) {
            // Skip restored event if the model doesn't use SoftDeletes
            if ($event === 'restored' && !method_exists($className, 'restored')) {
                \Illuminate\Support\Facades\Log::info("Skipping {$event} event for {$className} - method not found");
                continue;
            }
            
            // Log that we're registering the event
            \Illuminate\Support\Facades\Log::info("Registering {$event} event for {$className}");
            
            // Register the event with error handling
            try {
                static::{$event}(function ($model) use ($event) {
                    $modelClass = get_class($model);
                    $modelId = $model->getKey();
                    \Illuminate\Support\Facades\Log::info(
                        "{$event} event triggered for {$modelClass} (ID: {$modelId})"
                    );
                    
                    // Log the model's attributes for debugging
                    \Illuminate\Support\Facades\Log::info("Model attributes: " . json_encode($model->getAttributes()));
                    
                    // Call the logActivity method
                    $description = ucfirst($event) . ' ' . class_basename($model);
                    $model->logActivity($event, $description);
                    
                    \Illuminate\Support\Facades\Log::info("logActivity called for {$event} event on {$modelClass} (ID: {$modelId})");
                });
                
                \Illuminate\Support\Facades\Log::info("Successfully registered {$event} event for {$className}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error(
                    "Failed to register {$event} event for {$className}: " . $e->getMessage(),
                    ['exception' => $e]
                );
            }
        }
    }

    /**
     * Log an activity for the model.
     *
     * @param string $eventName
     * @param string $description Optional custom description for the activity
     * @return void
     */
    public function logActivity(string $eventName, string $description = '')
    {
        // Debug: Log that the method was called
        \Illuminate\Support\Facades\Log::info('logActivity method called', [
            'event' => $eventName,
            'description' => $description,
            'model' => get_class($this),
            'model_id' => $this->getKey(),
            'time' => now()->toDateTimeString()
        ]);
        
        // Additional debug to check if we can write to the log
        \Illuminate\Support\Facades\Log::info('Debug check - can write to log');
        
        $logName = $this->getLogNameToUse($eventName);
        \Illuminate\Support\Facades\Log::info('Using log name: ' . $logName);
        
        // If no description was provided, generate one
        if ($description === '') {
            $description = $this->getDescriptionForEvent($eventName);
            if ($description === '') {
                \Illuminate\Support\Facades\Log::warning('No description could be generated for event: ' . $eventName);
                return; // Skip if no description could be generated
            }
        }
        
        // Get the causer ID and type
        $causerId = null;
        $causerType = null;
        
        if (Auth::check()) {
            $causerId = Auth::id();
            $causerType = get_class(Auth::user());
            \Illuminate\Support\Facades\Log::info('Authenticated user - causer ID: ' . $causerId . ', type: ' . $causerType);
        } elseif (app()->runningInConsole()) {
            $causerId = 1; // System user
            $causerType = 'App\\Models\\User';
            \Illuminate\Support\Facades\Log::info('Console context - using system user as causer');
        } else {
            \Illuminate\Support\Facades\Log::warning('No authenticated user and not in console context');
        }
        
        // Get IP address and user agent
        $ipAddress = request()->ip() ?? 'console';
        $userAgent = request()->userAgent() ?? 'console';
        \Illuminate\Support\Facades\Log::info('IP: ' . $ipAddress . ', User Agent: ' . $userAgent);
        
        // Get the properties for the activity
        $properties = $this->getActivityProperties($eventName);
        \Illuminate\Support\Facades\Log::info('Activity properties:', $properties);
        
        try {
            // Create the activity log directly using our Activity model
            $activity = new \App\Models\Activity();
            \Illuminate\Support\Facades\Log::info('Created new Activity instance');
            
            // Prepare the activity data
            $activityData = [
                'log_name' => $logName,
                'properties' => $properties,
                'type_id' => 1, // Default type
                'category_id' => 1, // Default category
                'title' => $description,
                'description' => $description,
                'action' => $this->getActionForEvent($eventName),
                'details' => json_encode($properties),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'metadata' => json_encode([
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'context' => app()->runningInConsole() ? 'console' : 'web',
                ]),
                'subject_id' => $this->getKey(),
                'subject_type' => get_class($this),
                'causer_id' => $causerId,
                'causer_type' => $causerType,
                '_status' => 1, // Completed
                'created_at' => now(),
            ];
            
            \Illuminate\Support\Facades\Log::info('Prepared activity data:', $activityData);
            
            // Fill the activity
            $activity->fill($activityData);
            \Illuminate\Support\Facades\Log::info('Filled activity model');
            
            // Debug: Check if the model is dirty
            if ($activity->isDirty()) {
                $dirty = $activity->getDirty();
                \Illuminate\Support\Facades\Log::info('Dirty attributes:', $dirty);
                
                // Log each dirty attribute with its value
                foreach ($dirty as $key => $value) {
                    \Illuminate\Support\Facades\Log::info("Dirty - $key: " . (is_array($value) || is_object($value) ? json_encode($value) : $value));
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('No dirty attributes after fill');
            }
            
            // Save the activity
            \Illuminate\Support\Facades\Log::info('Attempting to save activity...');
            $saved = $activity->save();
            
            // Debug: Log save result
            if ($saved) {
                \Illuminate\Support\Facades\Log::info('Activity saved successfully with ID: ' . $activity->id);
            } else {
                \Illuminate\Support\Facades\Log::error('Failed to save activity');
                if (method_exists($activity, 'getErrors')) {
                    \Illuminate\Support\Facades\Log::error('Save errors:', $activity->getErrors() ?? []);
                }
                if (method_exists($activity, 'getConnection') && method_exists($activity->getConnection(), 'getQueryLog')) {
                    \Illuminate\Support\Facades\Log::error('Query log:', $activity->getConnection()->getQueryLog() ?? []);
                }
            }
            
            return $saved;
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception in logActivity: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Get the action for the event.
     *
     * @param string $eventName
     * @return string
     */
    protected function getActionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                return 'created';
            case 'updated':
                return 'updated';
            case 'deleted':
                return 'deleted';
            case 'restored':
                return 'restored';
            default:
                return 'performed';
        }
    }
    
    /**
     * Get the properties for the activity log.
     *
     * @param string $eventName
     * @return array
     */
    protected function getActivityProperties(string $eventName): array
    {
        $properties = [];
        
        if ($eventName === 'updated') {
            $properties = [
                'old' => array_intersect_key(
                    $this->oldAttributes ?? [],
                    $this->attributes
                ),
                'attributes' => $this->getDirty()
            ];
        } elseif ($eventName === 'deleted') {
            $properties = $this->attributesToArray();
        } else {
            $properties = $this->attributesToArray();
        }
        
        // Ensure we don't include any sensitive data
        if (method_exists($this, 'getHidden') && !empty($this->getHidden())) {
            $properties = array_diff_key($properties, array_flip($this->getHidden()));
        }
        
        return $properties;
    }
    
    /**
     * Get the log name to use for the event.
     *
     * @param string $eventName
     * @return string
     */
    protected function getLogNameToUse(string $eventName): string
    {
        if (method_exists($this, 'getLogName')) {
            return $this->getLogName($eventName);
        }
        
        return 'default';
    }
    
    /**
     * Get the description for the event.
     *
     * @param string $eventName
     * @return string
     */
    protected function getDescriptionForEvent(string $eventName): string
    {
        $class = class_basename($this);
        
        switch ($eventName) {
            case 'created':
                return "Created {$class}";
            case 'updated':
                return "Updated {$class}";
            case 'deleted':
                return "Deleted {$class}";
            case 'restored':
                return "Restored {$class}";
            default:
                return "{$eventName} {$class}";
        }
    }
}
