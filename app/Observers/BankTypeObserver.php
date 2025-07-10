<?php

namespace App\Observers;

use App\Models\BankType;

class BankTypeObserver
{
    /**
     * Handle the BankType "created" event.
     */
    public function created(BankType $bankType): void
    {
        //
    }

    /**
     * Handle the BankType "updated" event.
     */
    public function updated(BankType $bankType): void
    {
        //
    }

    /**
     * Handle the BankType "deleted" event.
     */
    public function deleted(BankType $bankType): void
    {
        //
    }

    /**
     * Handle the BankType "restored" event.
     */
    public function restored(BankType $bankType): void
    {
        //
    }

    /**
     * Handle the BankType "force deleted" event.
     */
    public function forceDeleted(BankType $bankType): void
    {
        //
    }
}
