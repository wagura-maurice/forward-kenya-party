<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Events\ActivityEvent;

class PreventDefaultActivityLogging
{
    /**
     * Handle the event.
     *
     * @param  \Spatie\Activitylog\Events\ActivityEvent  $event
     * @return void
     */
    public function handle(ActivityEvent $event)
    {
        // Prevent the default activity from being logged
        return false;
    }
}
