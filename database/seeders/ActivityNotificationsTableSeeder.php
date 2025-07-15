<?php

namespace Database\Seeders;

use App\Models\ActivityNotification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityNotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate the table
        ActivityNotification::truncate();
        
        // Get some users to associate with notifications
        $users = User::take(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UsersTableSeeder first.');
            return;
        }
        
        $notifications = [
            [
                'title' => 'Welcome to Our Platform',
                'message' => 'Thank you for registering with us! We are excited to have you on board.',
                '_status' => ActivityNotification::SENT,
                'data' => ['type' => 'welcome'],
            ],
            [
                'title' => 'New Feature Released',
                'message' => 'We have released a new feature. Check it out in your dashboard!',
                '_status' => ActivityNotification::SENT,
                'data' => ['type' => 'feature_update'],
            ],
            [
                'title' => 'Scheduled Maintenance',
                'message' => 'We will be performing scheduled maintenance on our platform tomorrow from 2 AM to 4 AM UTC.',
                '_status' => ActivityNotification::PENDING,
                'data' => ['type' => 'maintenance'],
            ],
            [
                'title' => 'Account Update Required',
                'message' => 'Please update your profile information to continue using our services.',
                '_status' => ActivityNotification::SENT,
                'data' => ['type' => 'account_update'],
            ],
            [
                'title' => 'Payment Received',
                'message' => 'We have successfully processed your payment of $99.00. Thank you for your business!',
                '_status' => ActivityNotification::SENT,
                'data' => ['type' => 'payment'],
            ],
        ];
        
        $now = now();
        $records = [];
        
        foreach ($users as $user) {
            foreach ($notifications as $notification) {
                $records[] = array_merge($notification, [
                    'user_id' => $user->id,
                    'activity_id' => 1, // Assuming activity with ID 1 exists
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'sent_at' => $notification['_status'] === ActivityNotification::SENT ? $now : null,
                    'read_at' => $notification['_status'] === ActivityNotification::READ ? $now : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
        
        // Insert all records in a single query for better performance
        ActivityNotification::insert($records);
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('Seeded ' . count($records) . ' activity notifications.');
    }
}
