<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\User;

class MemberObserver
{
    /**
     * Handle the Member "created" event.
     */
    public function created(Member $member): void
    {
        // Log the creation of a new member
        activity()
            ->causedBy(auth()->user())
            ->performedOn($member)
            ->log('Member created: ' . $member->party_membership_number);
    }

    /**
     * Handle the Member "updated" event.
     */
    public function updated(Member $member): void
    {
        // Log the update of a member
        activity()
            ->causedBy(auth()->user())
            ->performedOn($member)
            ->log('Member updated: ' . $member->party_membership_number);
    }

    /**
     * Handle the Member "deleted" event.
     */
    public function deleted(Member $member): void
    {
        // Log the deletion of a member
        activity()
            ->causedBy(auth()->user())
            ->performedOn($member)
            ->log('Member deleted: ' . $member->party_membership_number);
    }

    /**
     * Handle the Member "restored" event.
     */
    public function restored(Member $member): void
    {
        // Log the restoration of a member
        activity()
            ->causedBy(auth()->user())
            ->performedOn($member)
            ->log('Member restored: ' . $member->party_membership_number);
    }

    /**
     * Handle the Member "force deleted" event.
     */
    public function forceDeleted(Member $member): void
    {
        // Log the force deletion of a member
        activity()
            ->causedBy(auth()->user())
            ->performedOn($member)
            ->log('Member force deleted: ' . $member->party_membership_number);
    }
}
