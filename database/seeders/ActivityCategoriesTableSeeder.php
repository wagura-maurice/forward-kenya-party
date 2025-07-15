<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ActivityCategory;

class ActivityCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the activity_categories table
        \DB::table('activity_categories')->delete();

        // Define e-government activity categories
        $categories = [
            [
                'name' => 'Citizen Engagement',
                'slug' => Str::slug('Citizen Engagement'),
                'description' => 'Activities involving citizen participation, feedback, and public consultations.',
                'configuration' => json_encode(['allow_feedback' => true, 'allow_anonymous' => true]),
            ],
            [
                'name' => 'Policy and Governance',
                'slug' => Str::slug('Policy and Governance'),
                'description' => 'Activities related to policy development, governance, and regulatory compliance.',
                'configuration' => json_encode(['allow_document_upload' => true, 'allow_versioning' => true]),
            ],
            [
                'name' => 'Service Delivery',
                'slug' => Str::slug('Service Delivery'),
                'description' => 'Activities focused on providing government services to citizens and businesses.',
                'configuration' => json_encode(['allow_service_requests' => true, 'allow_status_updates' => true]),
            ],
            [
                'name' => 'Capacity Building',
                'slug' => Str::slug('Capacity Building'),
                'description' => 'Activities aimed at training government staff and building institutional capacity.',
                'configuration' => json_encode(['allow_registration' => true, 'allow_certification' => true]),
            ],
            [
                'name' => 'Public Awareness',
                'slug' => Str::slug('Public Awareness'),
                'description' => 'Activities designed to inform and educate the public about government initiatives.',
                'configuration' => json_encode(['allow_multimedia' => true, 'allow_sharing' => true]),
            ],
            [
                'name' => 'Data and Analytics',
                'slug' => Str::slug('Data and Analytics'),
                'description' => 'Activities involving the collection, analysis, and reporting of government data.',
                'configuration' => json_encode(['allow_data_upload' => true, 'allow_analytics' => true]),
            ],
            [
                'name' => 'Infrastructure and Development',
                'slug' => Str::slug('Infrastructure and Development'),
                'description' => 'Activities related to the planning, construction, and maintenance of public infrastructure.',
                'configuration' => json_encode(['allow_project_proposals' => true, 'allow_progress_tracking' => true]),
            ],
            [
                'name' => 'Emergency and Disaster Management',
                'slug' => Str::slug('Emergency and Disaster Management'),
                'description' => 'Activities focused on responding to emergencies and disasters.',
                'configuration' => json_encode(['allow_emergency_alerts' => true, 'allow_resource_allocation' => true]),
            ],
            [
                'name' => 'Environmental Sustainability',
                'slug' => Str::slug('Environmental Sustainability'),
                'description' => 'Activities aimed at protecting and conserving the environment.',
                'configuration' => json_encode(['allow_public_participation' => true, 'allow_data_reporting' => true]),
            ],
            [
                'name' => 'Digital Transformation',
                'slug' => Str::slug('Digital Transformation'),
                'description' => 'Activities focused on adopting digital technologies to improve government services.',
                'configuration' => json_encode(['allow_innovation_proposals' => true, 'allow_technology_adoption' => true]),
            ],
            [
                'name' => 'Legal and Compliance',
                'slug' => Str::slug('Legal and Compliance'),
                'description' => 'Activities related to ensuring compliance with laws and regulations.',
                'configuration' => json_encode(['allow_document_submission' => true, 'allow_audit_trails' => true]),
            ],
            [
                'name' => 'International Relations',
                'slug' => Str::slug('International Relations'),
                'description' => 'Activities involving collaboration with international organizations and governments.',
                'configuration' => json_encode(['allow_diplomatic_engagement' => true, 'allow_funding_proposals' => true]),
            ],
            [
                'name' => 'Research and Innovation',
                'slug' => Str::slug('Research and Innovation'),
                'description' => 'Activities focused on research and innovation in government programs.',
                'configuration' => json_encode(['allow_research_proposals' => true, 'allow_publication' => true]),
            ],
            [
                'name' => 'Social Welfare',
                'slug' => Str::slug('Social Welfare'),
                'description' => 'Activities aimed at providing social welfare services to citizens.',
                'configuration' => json_encode(['allow_benefit_application' => true, 'allow_case_management' => true]),
            ],
            [
                'name' => 'Public Health',
                'slug' => Str::slug('Public Health'),
                'description' => 'Activities focused on improving public health and healthcare services.',
                'configuration' => json_encode(['allow_health_data_collection' => true, 'allow_campaign_management' => true]),
            ],
        ];

        // Insert the activity categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            ActivityCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}