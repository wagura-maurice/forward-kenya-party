<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ServiceType;

class ServiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the service_types table
        \DB::table('service_types')->delete();

        // Define Forward Kenya Party service types
        $types = [
            [
                'name' => 'Membership',
                'slug' => Str::slug('Membership'),
                'description' => 'Joining and maintaining party membership.',
                'configuration' => json_encode(['allow_registration' => true]),
            ],
            [
                'name' => 'Events',
                'slug' => Str::slug('Events'),
                'description' => 'Participation in party rallies, conferences, and organized events.',
                'configuration' => json_encode(['allow_event_signup' => true]),
            ],
            [
                'name' => 'Volunteering',
                'slug' => Str::slug('Volunteering'),
                'description' => 'Volunteering for party activities and outreach.',
                'configuration' => json_encode(['allow_volunteer_signup' => true]),
            ],
            [
                'name' => 'Policy',
                'slug' => Str::slug('Policy'),
                'description' => 'Submission and discussion of party policy ideas and feedback.',
                'configuration' => json_encode(['allow_policy_suggestion' => true]),
            ],
            [
                'name' => 'Donations',
                'slug' => Str::slug('Donations'),
                'description' => 'Support the party financially through donations.',
                'configuration' => json_encode(['allow_donations' => true]),
            ],
            [
                'name' => 'Leadership',
                'slug' => Str::slug('Leadership'),
                'description' => 'Leadership positions and nominations.',
                'configuration' => json_encode(['allow_leadership_application' => true]),
            ],
            [
                'name' => 'Youth Affairs',
                'slug' => Str::slug('Youth Affairs'),
                'description' => 'Youth engagement and empowerment programs.',
                'configuration' => json_encode(['allow_youth_programs' => true]),
            ],
            [
                'name' => 'News & Updates',
                'slug' => Str::slug('News & Updates'),
                'description' => 'Subscription to party news, newsletters, and updates.',
                'configuration' => json_encode(['allow_news_subscription' => true]),
            ],
            [
                'name' => 'Grievance',
                'slug' => Str::slug('Grievance'),
                'description' => 'Submission of complaints, grievances, and suggestions.',
                'configuration' => json_encode(['allow_grievance_submission' => true]),
            ],
            [
                'name' => 'Training & Workshops',
                'slug' => Str::slug('Training & Workshops'),
                'description' => 'Participate in training, workshops, and educational programs.',
                'configuration' => json_encode(['allow_training_signup' => true]),
            ],
        ];

        // Insert the service types into the database using Eloquent
        foreach ($types as $key => $type) {
            ServiceType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}