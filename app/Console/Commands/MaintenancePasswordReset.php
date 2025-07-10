<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class MaintenancePasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:maintenance-password-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that runs every 3 months to reset the password of every user as a security measure. This is part of the maintenance contract offered by the developer.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting the maintenance password reset process...');

        try {
            // Begin transaction
            DB::beginTransaction();

            // Fetch all users
            $users = User::all();

            foreach ($users as $user) {
                // Set email_verified_at to null
                $user->email_verified_at = null;

                // Set a placeholder password
                $user->password = bcrypt('N/A');
                
                // Save the user
                $user->save();

                // Send password reset email
                Password::sendResetLink(['email' => $user->email]);
            }

            // Commit transaction
            DB::commit();

            $this->info('Password reset emails have been sent to all users.');
        } catch (\Throwable $th) {
            // Rollback transaction
            DB::rollBack();

            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
