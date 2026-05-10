<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollingStreamCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('polling_stream_categories')->delete();

        $categories = [
            [
                'name' => 'Presidential Election Stream',
                'slug' => Str::slug('Presidential Election Stream'),
                'description' => 'Voting stream for presidential election candidates and results',
                'configuration' => json_encode([
                    'election_type' => 'presidential',
                    'priority_level' => 'national',
                    'results_transmission_required' => true,
                    'security_level' => 'high',
                    'verification_steps' => ['station_level', 'constituency_level', 'county_level', 'national_level']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Governor Election Stream',
                'slug' => Str::slug('Governor Election Stream'),
                'description' => 'Voting stream for county governor election candidates and results',
                'configuration' => json_encode([
                    'election_type' => 'gubernatorial',
                    'priority_level' => 'county',
                    'results_transmission_required' => true,
                    'security_level' => 'high',
                    'verification_steps' => ['station_level', 'ward_level', 'constituency_level', 'county_level']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Senator Election Stream',
                'slug' => Str::slug('Senator Election Stream'),
                'description' => 'Voting stream for county senator election candidates and results',
                'configuration' => json_encode([
                    'election_type' => 'senatorial',
                    'priority_level' => 'county',
                    'results_transmission_required' => true,
                    'security_level' => 'high',
                    'verification_steps' => ['station_level', 'constituency_level', 'county_level']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Women Representative Stream',
                'slug' => Str::slug('Women Representative Stream'),
                'description' => 'Voting stream for county women representative election candidates and results',
                'configuration' => json_encode([
                    'election_type' => 'women_representative',
                    'priority_level' => 'county',
                    'results_transmission_required' => true,
                    'security_level' => 'medium',
                    'verification_steps' => ['station_level', 'constituency_level', 'county_level']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member of Parliament Stream',
                'slug' => Str::slug('Member of Parliament Stream'),
                'description' => 'Voting stream for constituency member of parliament election candidates and results',
                'configuration' => json_encode([
                    'election_type' => 'parliamentary',
                    'priority_level' => 'constituency',
                    'results_transmission_required' => true,
                    'security_level' => 'high',
                    'verification_steps' => ['station_level', 'constituency_level']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Member of County Assembly Stream',
                'slug' => Str::slug('Member of County Assembly Stream'),
                'description' => 'Voting stream for ward member of county assembly election candidates and results',
                'configuration' => json_encode([
                    'election_type' => 'mca',
                    'priority_level' => 'ward',
                    'results_transmission_required' => true,
                    'security_level' => 'medium',
                    'verification_steps' => ['station_level', 'ward_level']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Referendum Stream',
                'slug' => Str::slug('Referendum Stream'),
                'description' => 'Voting stream for constitutional referendum questions and results',
                'configuration' => json_encode([
                    'election_type' => 'referendum',
                    'priority_level' => 'national',
                    'results_transmission_required' => true,
                    'security_level' => 'high',
                    'verification_steps' => ['station_level', 'constituency_level', 'county_level', 'national_level'],
                    'vote_type' => 'yes_no'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('polling_stream_categories')->insert($categories);
    }
}
