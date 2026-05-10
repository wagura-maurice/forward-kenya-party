<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollingStationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('polling_station_types')->delete();

        $types = [
            [
                'name' => 'Regular Polling Station',
                'slug' => Str::slug('Regular Polling Station'),
                'description' => 'Standard polling station for general voting in residential or commercial areas',
                'configuration' => json_encode([
                    'type' => 'regular',
                    'voter_capacity' => '500-1000',
                    'requires_iebc_supervision' => true,
                    'can_host_advanced_voting' => false,
                    'accessibility_features' => ['ramp', 'wheelchair_accessible']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Institutional Polling Station',
                'slug' => Str::slug('Institutional Polling Station'),
                'description' => 'Polling station located within institutions like schools, hospitals, or government buildings',
                'configuration' => json_encode([
                    'type' => 'institutional',
                    'voter_capacity' => '300-800',
                    'requires_iebc_supervision' => true,
                    'can_host_advanced_voting' => false,
                    'facility_type' => 'institution',
                    'shared_facility' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Special Voting Station',
                'slug' => Str::slug('Special Voting Station'),
                'description' => 'Dedicated station for special voting categories including security forces, healthcare workers, and IEBC staff',
                'configuration' => json_encode([
                    'type' => 'special',
                    'voter_capacity' => '50-200',
                    'requires_iebc_supervision' => true,
                    'can_host_advanced_voting' => true,
                    'restricted_access' => true,
                    'special_categories' => ['security', 'healthcare', 'iebc_staff', 'media']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tallying Station',
                'slug' => Str::slug('Tallying Station'),
                'description' => 'Station designated for vote counting and results compilation at constituency level',
                'configuration' => json_encode([
                    'type' => 'tallying',
                    'voter_capacity' => '0',
                    'requires_iebc_supervision' => true,
                    'can_host_advanced_voting' => false,
                    'function' => 'counting',
                    'security_level' => 'high',
                    'results_transmission' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile Polling Station',
                'slug' => Str::slug('Mobile Polling Station'),
                'description' => 'Temporary polling setup for remote, pastoral, or hard-to-reach areas',
                'configuration' => json_encode([
                    'type' => 'mobile',
                    'voter_capacity' => '100-300',
                    'requires_iebc_supervision' => true,
                    'can_host_advanced_voting' => false,
                    'temporary_setup' => true,
                    'mobility_required' => true,
                    'target_areas' => ['pastoral', 'remote', 'island', 'disaster_affected']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hospital Polling Station',
                'slug' => Str::slug('Hospital Polling Station'),
                'description' => 'Station within healthcare facilities for patients and healthcare workers',
                'configuration' => json_encode([
                    'type' => 'hospital',
                    'voter_capacity' => '50-150',
                    'requires_iebc_supervision' => true,
                    'can_host_advanced_voting' => true,
                    'healthcare_facility' => true,
                    'patient_voting' => true,
                    'infection_control_protocols' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('polling_station_types')->insert($types);
    }
}
