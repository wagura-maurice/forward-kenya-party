<?php

namespace App\Policies;

use App\Models\ActivityNotification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityNotificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view their own notifications
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityNotification $notification): bool
    {
        // Users can only view their own notifications
        return $user->id === $notification->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin users can create notifications
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivityNotification $notification): bool
    {
        // Users can only update their own notifications
        return $user->id === $notification->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityNotification $notification): bool
    {
        // Users can only delete their own notifications
        return $user->id === $notification->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActivityNotification $notification): bool
    {
        // Only admin users can restore notifications
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ActivityNotification $notification): bool
    {
        // Only admin users can force delete notifications
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can mark notifications as read.
     */
    public function markAsRead(User $user, ActivityNotification $notification): bool
    {
        // Users can only mark their own notifications as read
        return $user->id === $notification->user_id;
    }
    
    /**
     * Determine whether the user can mark all notifications as read.
     */
    public function markAllAsRead(User $user): bool
    {
        // Any authenticated user can mark all their notifications as read
        return true;
    }
}
