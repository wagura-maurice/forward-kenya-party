<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Party Secretariat',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 1FKP.png',
                'description' => 'The central administrative hub coordinating all party activities, leadership, and strategy.',
                'website' => '#',
            ],
            [
                'name' => 'Membership & Mobilization',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 2FKP.png',
                'description' => 'Handles recruitment, registration, and engagement of party members and supporters across Kenya.',
                'website' => '#',
            ],
            [
                'name' => 'Communications & Media',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 3FKP.png',
                'description' => 'Manages party communications, press relations, branding, and digital presence.',
                'website' => '#',
            ],
            [
                'name' => 'Policy & Research',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 4FKP.png',
                'description' => 'Develops party policies, conducts research, and provides strategic advice on national issues.',
                'website' => '#',
            ],
            [
                'name' => 'Legal Affairs',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png',
                'description' => 'Ensures party compliance with laws and regulations, and offers legal support to members.',
                'website' => '#',
            ],
            [
                'name' => 'Finance & Fundraising',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 1FKP.png',
                'description' => 'Oversees party finances, budgeting, and fundraising initiatives.',
                'website' => '#',
            ],
            [
                'name' => 'Women Affairs',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 2FKP.png',
                'description' => 'Promotes the involvement and empowerment of women within the party and in national leadership.',
                'website' => '#',
            ],
            [
                'name' => 'Youth Affairs',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 3FKP.png',
                'description' => 'Engages the youth, develops youth-focused programs, and encourages youth participation in politics.',
                'website' => '#',
            ],
            [
                'name' => 'Election Board',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 4FKP.png',
                'description' => 'Manages party primaries, nominations, and election preparedness.',
                'website' => '#',
            ],
            [
                'name' => 'IT & Digital Strategy',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png',
                'description' => 'Leads party technology, digital security, and innovation efforts.',
                'website' => '#',
            ],
            [
                'name' => 'Partnerships & Outreach',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 1FKP.png',
                'description' => 'Builds alliances with civil society, government, and international partners.',
                'website' => '#',
            ],
            [
                'name' => 'Logistics & Events',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 2FKP.png',
                'description' => 'Organizes party events, rallies, and manages logistics for all activities.',
                'website' => '#',
            ],
            [
                'name' => 'Ethics & Compliance',
                'logo' => '/assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 3FKP.png',
                'description' => 'Ensures party integrity, handles disciplinary matters, and promotes ethical leadership.',
                'website' => '#',
            ],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'uuid' => (string) Str::uuid(),
                'type_id' => 1, // Replace with actual type_id
                'category_id' => 1, // Replace with actual category_id
                'name' => $department['name'],
                'slug' => Str::slug($department['name']),
                'description' => $department['description'],
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Department::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo' => $department['logo'],
                // 'contact_email' => 'example@ecitizen.go.ke', // Replace with actual email
                // 'contact_phone' => '+254700000000', // Replace with actual phone number
                'website' => $department['website'],
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
