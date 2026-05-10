<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollingStreamTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('polling_stream_types')->delete();

        $types = [
            [
                'name' => 'Regular Voting Stream',
                'slug' => Str::slug('Regular Voting Stream'),
                'description' => 'Standard voting stream for general election day voting',
                'configuration' => json_encode([
                    'stream_type' => 'regular',
                    'voting_method' => 'in_person',
                    'timing' => 'election_day',
                    'requires_verification' => true,
                    'ballot_type' => 'paper',
                    'counting_method' => 'manual'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Advanced Voting Stream',
                'slug' => Str::slug('Advanced Voting Stream'),
                'description' => 'Early voting stream for eligible voters who cannot vote on election day',
                'configuration' => json_encode([
                    'stream_type' => 'advanced',
                    'voting_method' => 'in_person',
                    'timing' => 'pre_election',
                    'requires_verification' => true,
                    'ballot_type' => 'paper',
                    'counting_method' => 'secure_storage',
                    'special_eligibility' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Special Voting Stream',
                'slug' => Str::slug('Special Voting Stream'),
                'description' => 'Dedicated voting stream for essential service workers and security personnel',
                'configuration' => json_encode([
                    'stream_type' => 'special',
                    'voting_method' => 'in_person',
                    'timing' => 'pre_election',
                    'requires_verification' => true,
                    'ballot_type' => 'paper',
                    'counting_method' => 'secure_storage',
                    'restricted_categories' => ['security', 'healthcare', 'iebc_staff', 'media']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Electronic Voting Stream',
                'slug' => Str::slug('Electronic Voting Stream'),
                'description' => 'Electronic voting stream using biometric verification and digital systems',
                'configuration' => json_encode([
                    'stream_type' => 'electronic',
                    'voting_method' => 'electronic',
                    'timing' => 'election_day',
                    'requires_verification' => true,
                    'ballot_type' => 'digital',
                    'counting_method' => 'automated',
                    'biometric_required' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile Voting Stream',
                'slug' => Str::slug('Mobile Voting Stream'),
                'description' => 'Mobile voting stream for remote and hard-to-reach areas',
                'configuration' => json_encode([
                    'stream_type' => 'mobile',
                    'voting_method' => 'in_person',
                    'timing' => 'scheduled',
                    'requires_verification' => true,
                    'ballot_type' => 'paper',
                    'counting_method' => 'immediate',
                    'mobility_required' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hospital Voting Stream',
                'slug' => Str::slug('Hospital Voting Stream'),
                'description' => 'Voting stream for patients and healthcare workers in medical facilities',
                'configuration' => json_encode([
                    'stream_type' => 'hospital',
                    'voting_method' => 'in_person',
                    'timing' => 'pre_election',
                    'requires_verification' => true,
                    'ballot_type' => 'paper',
                    'counting_method' => 'secure_storage',
                    'healthcare_facility' => true,
                    'infection_control' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diaspora Voting Stream',
                'slug' => Str::slug('Diaspora Voting Stream'),
                'description' => 'Voting stream for Kenyan citizens living abroad',
                'configuration' => json_encode([
                    'stream_type' => 'diaspora',
                    'voting_method' => 'electronic',
                    'timing' => 'pre_election',
                    'requires_verification' => true,
                    'ballot_type' => 'digital',
                    'counting_method' => 'automated',
                    'international_access' => true,
                    'embassy_coordination' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tallying Stream',
                'slug' => Str::slug('Tallying Stream'),
                'description' => 'Results tallying and transmission stream for vote counting',
                'configuration' => json_encode([
                    'stream_type' => 'tallying',
                    'voting_method' => 'none',
                    'timing' => 'post_election',
                    'requires_verification' => true,
                    'ballot_type' => 'paper',
                    'counting_method' => 'manual_verification',
                    'results_transmission' => true,
                    'security_level' => 'high'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('polling_stream_types')->insert($types);
    }
}
