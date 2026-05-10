<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollingCenterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('polling_center_types')->delete();

        $types = [
            [
                'name' => 'Constituency Tallying Center',
                'slug' => Str::slug('Constituency Tallying Center'),
                'description' => 'Main center for constituency-level vote counting and results compilation',
                'configuration' => json_encode([
                    'level' => 'constituency',
                    'function' => 'tallying',
                    'can_host_results_transmission' => true,
                    'requires_security_clearance' => true,
                    'capacity' => 'large'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'County Tallying Center',
                'slug' => Str::slug('County Tallying Center'),
                'description' => 'Central hub for county-wide election results aggregation and verification',
                'configuration' => json_encode([
                    'level' => 'county',
                    'function' => 'tallying',
                    'can_host_results_transmission' => true,
                    'requires_security_clearance' => true,
                    'capacity' => 'very_large',
                    'backup_systems' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ward Polling Center',
                'slug' => Str::slug('Ward Polling Center'),
                'description' => 'Local polling center serving multiple polling stations within a ward',
                'configuration' => json_encode([
                    'level' => 'ward',
                    'function' => 'voting',
                    'can_host_results_transmission' => false,
                    'requires_security_clearance' => false,
                    'capacity' => 'medium'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IEBC Regional Office',
                'slug' => Str::slug('IEBC Regional Office'),
                'description' => 'Official IEBC regional office serving as administrative and coordination center',
                'configuration' => json_encode([
                    'level' => 'regional',
                    'function' => 'administrative',
                    'can_host_results_transmission' => true,
                    'requires_security_clearance' => true,
                    'capacity' => 'medium',
                    'official_iebc_facility' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Community Center',
                'slug' => Str::slug('Community Center'),
                'description' => 'Community-based facility adapted for polling purposes during elections',
                'configuration' => json_encode([
                    'level' => 'community',
                    'function' => 'voting',
                    'can_host_results_transmission' => false,
                    'requires_security_clearance' => false,
                    'capacity' => 'small',
                    'temporary_setup' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('polling_center_types')->insert($types);
    }
}
