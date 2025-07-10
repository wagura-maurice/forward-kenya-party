<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the services data
        $services = [
            [
                'uuid' => Str::uuid(),
                'type_id' => 1,
                'category_id' => 1,
                'name' => 'Membership Registration',
                'slug' => 'membership-registration',
                'description' => 'Join Forward Kenya Party and become an official member.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 1FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 2,
                'category_id' => 2,
                'name' => 'Event Participation',
                'slug' => 'event-participation',
                'description' => 'Sign up for rallies, conferences, and party events.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 2FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 3,
                'category_id' => 3,
                'name' => 'Volunteer Signup',
                'slug' => 'volunteer-signup',
                'description' => 'Volunteer for party activities and community outreach.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 3FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 4,
                'category_id' => 4,
                'name' => 'Policy Suggestion',
                'slug' => 'policy-suggestion',
                'description' => 'Submit your ideas and feedback to help shape party policy.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 4FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 5,
                'category_id' => 5,
                'name' => 'Donations',
                'slug' => 'donations',
                'description' => 'Support Forward Kenya Party through financial contributions.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 6,
                'category_id' => 6,
                'name' => 'Leadership Application',
                'slug' => 'leadership-application',
                'description' => 'Apply for leadership and nomination positions within the party.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 1FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 7,
                'category_id' => 7,
                'name' => 'Women Empowerment Program',
                'slug' => 'women-empowerment',
                'description' => 'Participate in women-focused initiatives and leadership training.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 2FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 8,
                'category_id' => 8,
                'name' => 'Youth Program',
                'slug' => 'youth-program',
                'description' => 'Join youth engagement, mentorship, and empowerment programs.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 3FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 9,
                'category_id' => 9,
                'name' => 'Party News Subscription',
                'slug' => 'party-news-subscription',
                'description' => 'Subscribe to receive party news, updates, and newsletters.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 4FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 10,
                'category_id' => 10,
                'name' => 'Grievance Submission',
                'slug' => 'grievance-submission',
                'description' => 'Submit complaints, grievances, or suggestions to party leadership.',
                'configuration' => json_encode(['key' => 'value']),
                '_status' => Service::ACTIVE,
                'logo_path' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png',
                'is_featured' => true,
                'requires_payment' => false,
            ],
        ];

        // Insert the data into the services table
        DB::table('services')->insert($services);
    }
}
