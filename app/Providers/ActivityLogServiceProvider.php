<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Activity;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Set the default activity type ID for user-related activities
        $this->app['config']->set('activitylog.default_activity_type_id', 1);
        
        // Register event listeners
        $this->registerEventListeners();
    }
    
    /**
     * Register event listeners to prevent default activity logging.
     */
    protected function registerEventListeners()
    {
        // Get the event dispatcher instance
        $dispatcher = $this->app['events'];
        
        // Listen for all activity events and prevent default handling
        $dispatcher->listen('eloquent.saving: Spatie\\Activitylog\\Models\\Activity', 
            'App\\Listeners\\PreventDefaultActivityLogging@handle'
        );
        
        // Also listen for the specific activity event
        $dispatcher->listen('Spatie\\Activitylog\\Events\\ActivityEvent', 
            'App\\Listeners\\PreventDefaultActivityLogging@handle'
        );
    }

    /**
     * Register services.
     */
    public function register()
    {
        // Bind our custom activity model
        $this->app->bind(ActivityContract::class, Activity::class);
        
        // Completely disable the activity logger
        $this->app->singleton('activity', function () {
            return new class {
                public function __call($name, $arguments)
                {
                    // No-op - prevent any logging through the default logger
                    return $this;
                }
            };
        });
    }
}
