<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartmentServiceTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('department_service')->delete();
        
        \DB::table('department_service')->insert([
            // 1. Membership Registration -> Membership & Mobilization
            [
                'service_id' => 1,
                'department_id' => 2,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 2. Event Participation -> Logistics & Events
            [
                'service_id' => 2,
                'department_id' => 12,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 3. Volunteer Signup -> Partnerships & Outreach
            [
                'service_id' => 3,
                'department_id' => 11,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 4. Policy Suggestion -> Policy & Research
            [
                'service_id' => 4,
                'department_id' => 4,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 5. Donations -> Finance & Fundraising
            [
                'service_id' => 5,
                'department_id' => 6,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 6. Leadership Application -> Party Secretariat
            [
                'service_id' => 6,
                'department_id' => 1,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 7. Women Empowerment Program -> Women Affairs
            [
                'service_id' => 7,
                'department_id' => 7,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 8. Youth Program -> Youth Affairs
            [
                'service_id' => 8,
                'department_id' => 8,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 9. Party News Subscription -> Communications & Media
            [
                'service_id' => 9,
                'department_id' => 3,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
            // 10. Grievance Submission -> Ethics & Compliance
            [
                'service_id' => 10,
                'department_id' => 13,
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ],
        ]);
    }
}