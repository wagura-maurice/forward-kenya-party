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

        // Define e-government service types
        $types = [
            [
                'name' => 'Online Application',
                'slug' => Str::slug('Online Application'),
                'description' => 'Services that allow citizens to apply for permits, licenses, or benefits online.',
                'configuration' => json_encode(['allow_document_upload' => true, 'allow_online_payment' => true]),
            ],
            [
                'name' => 'Information Portal',
                'slug' => Str::slug('Information Portal'),
                'description' => 'Platforms providing access to government information, policies, and public records.',
                'configuration' => json_encode(['allow_search' => true, 'allow_download' => true]),
            ],
            [
                'name' => 'E-Payment',
                'slug' => Str::slug('E-Payment'),
                'description' => 'Services enabling online payment of taxes, fees, and other government charges.',
                'configuration' => json_encode(['allow_multiple_payment_methods' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Appointment Scheduling',
                'slug' => Str::slug('Appointment Scheduling'),
                'description' => 'Services allowing citizens to schedule appointments with government agencies.',
                'configuration' => json_encode(['allow_calendar_integration' => true, 'allow_reminders' => true]),
            ],
            [
                'name' => 'Complaint Management',
                'slug' => Str::slug('Complaint Management'),
                'description' => 'Systems for submitting and tracking complaints or feedback about government services.',
                'configuration' => json_encode(['allow_anonymous_submission' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Public Records Access',
                'slug' => Str::slug('Public Records Access'),
                'description' => 'Services providing access to public records, such as land titles or court documents.',
                'configuration' => json_encode(['allow_search' => true, 'allow_document_download' => true]),
            ],
            [
                'name' => 'Emergency Services',
                'slug' => Str::slug('Emergency Services'),
                'description' => 'Services for reporting emergencies and accessing emergency response resources.',
                'configuration' => json_encode(['allow_location_tracking' => true, 'allow_emergency_contact' => true]),
            ],
            [
                'name' => 'Training and Certification',
                'slug' => Str::slug('Training and Certification'),
                'description' => 'Online training programs and certification services for citizens and businesses.',
                'configuration' => json_encode(['allow_course_enrollment' => true, 'allow_certificate_issuance' => true]),
            ],
            [
                'name' => 'Business Registration',
                'slug' => Str::slug('Business Registration'),
                'description' => 'Services for registering new businesses and managing business licenses.',
                'configuration' => json_encode(['allow_online_registration' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Health Services',
                'slug' => Str::slug('Health Services'),
                'description' => 'Online health services, including appointment booking and medical record access.',
                'configuration' => json_encode(['allow_online_booking' => true, 'allow_medical_record_access' => true]),
            ],
            [
                'name' => 'Social Welfare Services',
                'slug' => Str::slug('Social Welfare Services'),
                'description' => 'Services for applying and managing social welfare benefits and programs.',
                'configuration' => json_encode(['allow_benefit_application' => true, 'allow_case_tracking' => true]),
            ],
            [
                'name' => 'Environmental Services',
                'slug' => Str::slug('Environmental Services'),
                'description' => 'Services related to environmental regulations, permits, and sustainability programs.',
                'configuration' => json_encode(['allow_permit_application' => true, 'allow_public_participation' => true]),
            ],
            [
                'name' => 'Immigration Services',
                'slug' => Str::slug('Immigration Services'),
                'description' => 'Services for visa applications, immigration status checks, and border security.',
                'configuration' => json_encode(['allow_online_application' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Legal Services',
                'slug' => Str::slug('Legal Services'),
                'description' => 'Access to legal resources, court records, and justice system services.',
                'configuration' => json_encode(['allow_document_submission' => true, 'allow_case_tracking' => true]),
            ],
            [
                'name' => 'Tourism Services',
                'slug' => Str::slug('Tourism Services'),
                'description' => 'Services promoting tourism, cultural heritage, and recreational activities.',
                'configuration' => json_encode(['allow_event_registration' => true, 'allow_tourism_data_access' => true]),
            ],
            [
                'name' => 'Agriculture Services',
                'slug' => Str::slug('Agriculture Services'),
                'description' => 'Services supporting agricultural programs, rural development, and food security.',
                'configuration' => json_encode(['allow_subsidy_application' => true, 'allow_agricultural_data_access' => true]),
            ],
            [
                'name' => 'Energy and Utilities Services',
                'slug' => Str::slug('Energy and Utilities Services'),
                'description' => 'Services for managing energy resources, utilities, and infrastructure.',
                'configuration' => json_encode(['allow_service_requests' => true, 'allow_billing_information' => true]),
            ],
            [
                'name' => 'Digital Services',
                'slug' => Str::slug('Digital Services'),
                'description' => 'Services driving digital innovation, e-governance, and technology adoption.',
                'configuration' => json_encode(['allow_innovation_proposals' => true, 'allow_technology_adoption' => true]),
            ],
            [
                'name' => 'Labor and Employment Services',
                'slug' => Str::slug('Labor and Employment Services'),
                'description' => 'Services for labor regulations, employment opportunities, and workforce development.',
                'configuration' => json_encode(['allow_job_application' => true, 'allow_training_enrollment' => true]),
            ],
            [
                'name' => 'Foreign Affairs Services',
                'slug' => Str::slug('Foreign Affairs Services'),
                'description' => 'Services for international relations, diplomacy, and consular support.',
                'configuration' => json_encode(['allow_visa_application' => true, 'allow_diplomatic_engagement' => true]),
            ],
            [
                'name' => 'Disaster Management Services',
                'slug' => Str::slug('Disaster Management Services'),
                'description' => 'Services for disaster response, relief efforts, and emergency preparedness.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_resource_allocation' => true]),
            ],
            [
                'name' => 'Public Works Services',
                'slug' => Str::slug('Public Works Services'),
                'description' => 'Services for construction, maintenance, and management of public infrastructure.',
                'configuration' => json_encode(['allow_project_proposals' => true, 'allow_progress_tracking' => true]),
            ],
        ];

        // Insert the service types into the database using Eloquent
        foreach ($types as $key => $type) {
            ServiceType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}