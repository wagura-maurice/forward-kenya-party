<?php

namespace App\Observers;

use App\Events\ActivityNotificationCreated;
use App\Models\ActivityNotification;
use Illuminate\Support\Facades\Log;

class ActivityNotificationObserver
{
    /**
     * Handle the ActivityNotification "creating" event.
     */
    public function creating(ActivityNotification $notification): void
    {
        // Set default status if not provided
        if (!isset($notification->status)) {
            $notification->status = ActivityNotification::PENDING;
        }
        
        // Generate UUID if not provided
        if (empty($notification->uuid)) {
            $notification->uuid = (string) \Illuminate\Support\Str::uuid();
        }
    }

    /**
     * Handle the ActivityNotification "created" event.
     */
    public function created(ActivityNotification $notification): void
    {
        try {
            // Dispatch an event to handle notification sending
            event(new ActivityNotificationCreated($notification));
        } catch (\Exception $e) {
            Log::error('Failed to process activity notification: ' . $e->getMessage(), [
                'notification_id' => $notification->id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle the ActivityNotification "updating" event.
     */
    public function updating(ActivityNotification $notification): void
    {
        // If status is being updated to SENT, set the sent_at timestamp
        if ($notification->isDirty('_status') && $notification->status === ActivityNotification::SENT) {
            $notification->sent_at = now();
        }
        
        // If status is being updated to FAILED, set the failed_at timestamp
        if ($notification->isDirty('_status') && $notification->status === ActivityNotification::FAILED) {
            $notification->failed_at = now();
        }
    }

    /**
     * Handle the ActivityNotification "updated" event.
     */
    public function updated(ActivityNotification $notification): void
    {
        // Handle any post-update logic here
    }

    /**
     * Handle the ActivityNotification "deleting" event.
     */
    public function deleting(ActivityNotification $notification): void
    {
        // Add any pre-delete logic here
    }

    /**
     * Handle the ActivityNotification "deleted" event.
     */
    public function deleted(ActivityNotification $notification): void
    {
        // Clean up any related data if needed
    }

    /**
     * Handle the ActivityNotification "restored" event.
     */
    public function restored(ActivityNotification $notification): void
    {
        // Handle restore logic if needed
    }

    /**
     * Handle the ActivityNotification "force deleted" event.
     */
    public function forceDeleted(ActivityNotification $notification): void
    {
        // Handle force delete logic if needed
    }
}
