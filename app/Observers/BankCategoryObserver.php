<?php

namespace App\Observers;

use App\Models\BankCategory;

class BankCategoryObserver
{
    /**
     * Handle the BankCategory "created" event.
     */
    public function created(BankCategory $bankCategory): void
    {
        //
    }

    /**
     * Handle the BankCategory "updated" event.
     */
    public function updated(BankCategory $bankCategory): void
    {
        //
    }

    /**
     * Handle the BankCategory "deleted" event.
     */
    public function deleted(BankCategory $bankCategory): void
    {
        //
    }

    /**
     * Handle the BankCategory "restored" event.
     */
    public function restored(BankCategory $bankCategory): void
    {
        //
    }

    /**
     * Handle the BankCategory "force deleted" event.
     */
    public function forceDeleted(BankCategory $bankCategory): void
    {
        //
    }
}
