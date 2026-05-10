<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollingStationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('polling_station_categories')->delete();

        $categories = [
            [
                'name' => 'Urban Polling Station',
                'slug' => Str::slug('Urban Polling Station'),
                'description' => 'Polling station located in urban areas with high population density',
                'configuration' => json_encode([
                    'area_type' => 'urban',
                    'population_density' => 'high',
                    'expected_voter_turnout' => '70-85%',
                    'staffing_level' => 'high',
                    'security_level' => 'medium',
                    'infrastructure_requirements' => ['electricity', 'internet', 'security']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rural Polling Station',
                'slug' => Str::slug('Rural Polling Station'),
                'description' => 'Polling station located in rural areas with dispersed population',
                'configuration' => json_encode([
                    'area_type' => 'rural',
                    'population_density' => 'low',
                    'expected_voter_turnout' => '60-75%',
                    'staffing_level' => 'medium',
                    'security_level' => 'low',
                    'infrastructure_requirements' => ['generator', 'mobile_connectivity']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Peri-Urban Polling Station',
                'slug' => Str::slug('Peri-Urban Polling Station'),
                'description' => 'Polling station located in transitional areas between urban and rural zones',
                'configuration' => json_encode([
                    'area_type' => 'peri-urban',
                    'population_density' => 'medium',
                    'expected_voter_turnout' => '65-80%',
                    'staffing_level' => 'medium',
                    'security_level' => 'medium',
                    'infrastructure_requirements' => ['generator', 'internet_optional']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Institutional Polling Station',
                'slug' => Str::slug('Institutional Polling Station'),
                'description' => 'Polling station within educational, healthcare, or government institutions',
                'configuration' => json_encode([
                    'area_type' => 'institutional',
                    'population_density' => 'variable',
                    'expected_voter_turnout' => '50-70%',
                    'staffing_level' => 'medium',
                    'security_level' => 'medium',
                    'infrastructure_requirements' => ['facility_support', 'security'],
                    'facility_types' => ['school', 'hospital', 'government_office', 'community_center']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Special Category Station',
                'slug' => Str::slug('Special Category Station'),
                'description' => 'Polling station for special voting categories and vulnerable groups',
                'configuration' => json_encode([
                    'area_type' => 'special',
                    'population_density' => 'controlled',
                    'expected_voter_turnout' => '40-60%',
                    'staffing_level' => 'high',
                    'security_level' => 'high',
                    'infrastructure_requirements' => ['medical_support', 'accessibility', 'privacy'],
                    'special_services' => ['wheelchair_access', 'sign_language', 'assisted_voting']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile/Outreach Station',
                'slug' => Str::slug('Mobile/Outreach Station'),
                'description' => 'Temporary polling station for remote, pastoral, or hard-to-reach communities',
                'configuration' => json_encode([
                    'area_type' => 'mobile',
                    'population_density' => 'very_low',
                    'expected_voter_turnout' => '30-50%',
                    'staffing_level' => 'low',
                    'security_level' => 'variable',
                    'infrastructure_requirements' => ['portable_equipment', 'satellite_communication'],
                    'mobility_features' => ['vehicle_required', 'portable_booths', 'generator']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('polling_station_categories')->insert($categories);
    }
}
