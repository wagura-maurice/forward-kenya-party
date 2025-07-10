<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ServiceCategory;

class ServiceCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the service_categories table
        \DB::table('service_categories')->delete();

        // Define e-government service categories
        $categories = [
            [
                'name' => 'Citizen Services',
                'slug' => Str::slug('Citizen Services'),
                'description' => 'Services related to citizen identification, certificates, and public records.',
                'configuration' => json_encode(['allow_anonymous' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Tax and Revenue',
                'slug' => Str::slug('Tax and Revenue'),
                'description' => 'Online tax filing, payment, and revenue management for individuals and businesses.',
                'configuration' => json_encode(['allow_attachments' => true, 'allow_online_payment' => true]),
            ],
            [
                'name' => 'Healthcare Services',
                'slug' => Str::slug('Healthcare Services'),
                'description' => 'Access to healthcare information, appointments, and medical records.',
                'configuration' => json_encode(['allow_sensitive_data' => true, 'allow_online_booking' => true]),
            ],
            [
                'name' => 'Education and E-Learning',
                'slug' => Str::slug('Education and E-Learning'),
                'description' => 'Online educational resources, student records, and e-learning platforms.',
                'configuration' => json_encode(['allow_file_uploads' => true, 'allow_certification' => true]),
            ],
            [
                'name' => 'Social Welfare and Benefits',
                'slug' => Str::slug('Social Welfare and Benefits'),
                'description' => 'Management of social benefits, pensions, and welfare programs.',
                'configuration' => json_encode(['allow_priority_requests' => true, 'allow_benefit_application' => true]),
            ],
            [
                'name' => 'Public Safety and Emergency Services',
                'slug' => Str::slug('Public Safety and Emergency Services'),
                'description' => 'Emergency response, law enforcement, and public safety initiatives.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_location_tracking' => true]),
            ],
            [
                'name' => 'Transportation and Infrastructure',
                'slug' => Str::slug('Transportation and Infrastructure'),
                'description' => 'Public transportation, road maintenance, and infrastructure projects.',
                'configuration' => json_encode(['allow_location_data' => true, 'allow_service_requests' => true]),
            ],
            [
                'name' => 'Environmental and Sustainability',
                'slug' => Str::slug('Environmental and Sustainability'),
                'description' => 'Environmental regulations, conservation efforts, and sustainability programs.',
                'configuration' => json_encode(['allow_environmental_data' => true, 'allow_public_participation' => true]),
            ],
            [
                'name' => 'Business and Licensing',
                'slug' => Str::slug('Business and Licensing'),
                'description' => 'Business registration, licensing, and commercial regulations.',
                'configuration' => json_encode(['allow_business_documents' => true, 'allow_online_registration' => true]),
            ],
            [
                'name' => 'Housing and Urban Development',
                'slug' => Str::slug('Housing and Urban Development'),
                'description' => 'Public housing, urban planning, and development projects.',
                'configuration' => json_encode(['allow_property_data' => true, 'allow_application_submission' => true]),
            ],
            [
                'name' => 'Immigration and Visa Services',
                'slug' => Str::slug('Immigration and Visa Services'),
                'description' => 'Immigration services, visa applications, and border security.',
                'configuration' => json_encode(['allow_passport_data' => true, 'allow_online_application' => true]),
            ],
            [
                'name' => 'Legal and Justice Services',
                'slug' => Str::slug('Legal and Justice Services'),
                'description' => 'Access to legal resources, court records, and justice system services.',
                'configuration' => json_encode(['allow_legal_documents' => true, 'allow_case_tracking' => true]),
            ],
            [
                'name' => 'Tourism and Cultural Heritage',
                'slug' => Str::slug('Tourism and Cultural Heritage'),
                'description' => 'Promotion of tourism, cultural heritage, and recreational activities.',
                'configuration' => json_encode(['allow_tourism_data' => true, 'allow_event_registration' => true]),
            ],
            [
                'name' => 'Agriculture and Rural Development',
                'slug' => Str::slug('Agriculture and Rural Development'),
                'description' => 'Agricultural programs, rural development, and food security initiatives.',
                'configuration' => json_encode(['allow_agricultural_data' => true, 'allow_subsidy_application' => true]),
            ],
            [
                'name' => 'Energy and Utilities',
                'slug' => Str::slug('Energy and Utilities'),
                'description' => 'Management of energy resources, utilities, and infrastructure.',
                'configuration' => json_encode(['allow_utility_data' => true, 'allow_service_requests' => true]),
            ],
            [
                'name' => 'Digital Transformation',
                'slug' => Str::slug('Digital Transformation'),
                'description' => 'Digital innovation, e-governance, and technology adoption across government services.',
                'configuration' => json_encode(['allow_innovation_proposals' => true, 'allow_technology_adoption' => true]),
            ],
            [
                'name' => 'Labor and Employment Services',
                'slug' => Str::slug('Labor and Employment Services'),
                'description' => 'Labor regulations, employment services, and workforce development.',
                'configuration' => json_encode(['allow_employment_data' => true, 'allow_job_application' => true]),
            ],
            [
                'name' => 'Foreign Affairs and Diplomacy',
                'slug' => Str::slug('Foreign Affairs and Diplomacy'),
                'description' => 'International relations, diplomacy, and consular services.',
                'configuration' => json_encode(['allow_diplomatic_data' => true, 'allow_visa_application' => true]),
            ],
            [
                'name' => 'Disaster Management and Relief',
                'slug' => Str::slug('Disaster Management and Relief'),
                'description' => 'Disaster response, relief efforts, and emergency preparedness.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_resource_allocation' => true]),
            ],
            [
                'name' => 'Public Works and Infrastructure',
                'slug' => Str::slug('Public Works and Infrastructure'),
                'description' => 'Construction, maintenance, and management of public infrastructure.',
                'configuration' => json_encode(['allow_project_proposals' => true, 'allow_progress_tracking' => true]),
            ],
        ];

        // Insert the service categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            ServiceCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}