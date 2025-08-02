<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\ActivitylogServiceProvider as SpatieActivitylogServiceProvider;

class ActivityLogServiceProvider extends SpatieActivitylogServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        parent::boot();

        // Set the default activity type ID for user-related activities
        $this->app['config']->set('activitylog.default_activity_type_id', 1);
    }

    /**
     * Register services.
     */
    public function register()
    {
        parent::register();

        // Register the activity logger with custom settings
        $this->app->singleton('activity', function ($app) {
            return tap($app['activitylog'], function ($activity) {
                $activity->setLogName('default');
                $activity->setCauser($app['auth']->user());
                $activity->setBatch(
                    $app['activitylog']->getUuidGenerator()->generate()
                );
            });
        });
    }
}
