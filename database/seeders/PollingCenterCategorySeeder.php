<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollingCenterCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('polling_center_categories')->delete();

        $categories = [
            [
                'name' => 'Primary Polling Center',
                'slug' => Str::slug('Primary Polling Center'),
                'description' => 'Main polling center for a constituency or ward, typically located in a central, accessible location',
                'configuration' => json_encode([
                    'level' => 'primary',
                    'can_host_multiple_stations' => true,
                    'requires_iebc_approval' => true,
                    'minimum_voter_threshold' => 1000
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Secondary Polling Center',
                'slug' => Str::slug('Secondary Polling Center'),
                'description' => 'Auxiliary polling center serving specific areas or communities within a constituency',
                'configuration' => json_encode([
                    'level' => 'secondary',
                    'can_host_multiple_stations' => true,
                    'requires_iebc_approval' => true,
                    'minimum_voter_threshold' => 500
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Special Polling Center',
                'slug' => Str::slug('Special Polling Center'),
                'description' => 'Dedicated polling center for special voting groups such as hospitals, prisons, or military installations',
                'configuration' => json_encode([
                    'level' => 'special',
                    'can_host_multiple_stations' => false,
                    'requires_iebc_approval' => true,
                    'minimum_voter_threshold' => 50,
                    'restricted_access' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile Polling Center',
                'slug' => Str::slug('Mobile Polling Center'),
                'description' => 'Temporary or mobile polling setup for remote or hard-to-reach areas',
                'configuration' => json_encode([
                    'level' => 'mobile',
                    'can_host_multiple_stations' => false,
                    'requires_iebc_approval' => true,
                    'minimum_voter_threshold' => 100,
                    'temporary_setup' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('polling_center_categories')->insert($categories);
    }
}
