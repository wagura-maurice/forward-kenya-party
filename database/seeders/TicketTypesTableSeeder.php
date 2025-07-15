<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\TicketType;

class TicketTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the ticket_types table
        \DB::table('ticket_types')->delete();

        // Define e-government ticket types
        $types = [
            [
                'name' => 'General Inquiry',
                'slug' => Str::slug('General Inquiry'),
                'description' => 'General inquiries and questions about government services.',
                'configuration' => json_encode(['allow_anonymous' => true, 'allow_attachments' => true]),
            ],
            [
                'name' => 'Service Request',
                'slug' => Str::slug('Service Request'),
                'description' => 'Requests for specific government services or assistance.',
                'configuration' => json_encode(['allow_service_selection' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Complaint',
                'slug' => Str::slug('Complaint'),
                'description' => 'Complaints about government services, policies, or staff.',
                'configuration' => json_encode(['allow_anonymous' => true, 'allow_evidence_upload' => true]),
            ],
            [
                'name' => 'Technical Support',
                'slug' => Str::slug('Technical Support'),
                'description' => 'Technical issues or support requests for government digital platforms.',
                'configuration' => json_encode(['allow_screenshots' => true, 'allow_remote_access' => true]),
            ],
            [
                'name' => 'Feedback and Suggestions',
                'slug' => Str::slug('Feedback and Suggestions'),
                'description' => 'Feedback or suggestions for improving government services.',
                'configuration' => json_encode(['allow_anonymous' => true, 'allow_public_display' => true]),
            ],
            [
                'name' => 'Billing and Payments',
                'slug' => Str::slug('Billing and Payments'),
                'description' => 'Issues or inquiries related to billing, payments, or financial transactions.',
                'configuration' => json_encode(['allow_receipt_upload' => true, 'allow_payment_disputes' => true]),
            ],
            [
                'name' => 'Policy and Regulation',
                'slug' => Str::slug('Policy and Regulation'),
                'description' => 'Inquiries or issues related to government policies and regulations.',
                'configuration' => json_encode(['allow_document_upload' => true, 'allow_legal_reference' => true]),
            ],
            [
                'name' => 'Public Safety',
                'slug' => Str::slug('Public Safety'),
                'description' => 'Reports or inquiries related to public safety and emergency services.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_location_tracking' => true]),
            ],
            [
                'name' => 'Environmental Issues',
                'slug' => Str::slug('Environmental Issues'),
                'description' => 'Reports or inquiries related to environmental concerns and sustainability.',
                'configuration' => json_encode(['allow_evidence_upload' => true, 'allow_public_participation' => true]),
            ],
            [
                'name' => 'Infrastructure and Maintenance',
                'slug' => Str::slug('Infrastructure and Maintenance'),
                'description' => 'Reports or requests related to public infrastructure and maintenance.',
                'configuration' => json_encode(['allow_location_pinning' => true, 'allow_progress_tracking' => true]),
            ],
            [
                'name' => 'Health Services',
                'slug' => Str::slug('Health Services'),
                'description' => 'Inquiries or issues related to healthcare services and medical records.',
                'configuration' => json_encode(['allow_sensitive_data' => true, 'allow_appointment_booking' => true]),
            ],
            [
                'name' => 'Education Services',
                'slug' => Str::slug('Education Services'),
                'description' => 'Inquiries or issues related to educational programs and student services.',
                'configuration' => json_encode(['allow_document_upload' => true, 'allow_certification_requests' => true]),
            ],
            [
                'name' => 'Social Welfare',
                'slug' => Str::slug('Social Welfare'),
                'description' => 'Inquiries or issues related to social welfare programs and benefits.',
                'configuration' => json_encode(['allow_benefit_application' => true, 'allow_case_tracking' => true]),
            ],
            [
                'name' => 'Immigration and Visa',
                'slug' => Str::slug('Immigration and Visa'),
                'description' => 'Inquiries or issues related to immigration services and visa applications.',
                'configuration' => json_encode(['allow_document_upload' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Legal Assistance',
                'slug' => Str::slug('Legal Assistance'),
                'description' => 'Requests for legal assistance or inquiries about legal services.',
                'configuration' => json_encode(['allow_case_submission' => true, 'allow_legal_document_upload' => true]),
            ],
            [
                'name' => 'Tourism and Culture',
                'slug' => Str::slug('Tourism and Culture'),
                'description' => 'Inquiries or issues related to tourism and cultural heritage services.',
                'configuration' => json_encode(['allow_event_registration' => true, 'allow_tourism_data_access' => true]),
            ],
            [
                'name' => 'Agriculture and Rural Development',
                'slug' => Str::slug('Agriculture and Rural Development'),
                'description' => 'Inquiries or issues related to agricultural programs and rural development.',
                'configuration' => json_encode(['allow_subsidy_application' => true, 'allow_agricultural_data_access' => true]),
            ],
            [
                'name' => 'Energy and Utilities',
                'slug' => Str::slug('Energy and Utilities'),
                'description' => 'Inquiries or issues related to energy resources and utility services.',
                'configuration' => json_encode(['allow_service_requests' => true, 'allow_billing_information' => true]),
            ],
            [
                'name' => 'Digital Transformation',
                'slug' => Str::slug('Digital Transformation'),
                'description' => 'Inquiries or issues related to digital innovation and e-governance.',
                'configuration' => json_encode(['allow_innovation_proposals' => true, 'allow_technology_adoption' => true]),
            ],
            [
                'name' => 'Labor and Employment',
                'slug' => Str::slug('Labor and Employment'),
                'description' => 'Inquiries or issues related to labor regulations and employment services.',
                'configuration' => json_encode(['allow_job_application' => true, 'allow_training_enrollment' => true]),
            ],
            [
                'name' => 'Foreign Affairs',
                'slug' => Str::slug('Foreign Affairs'),
                'description' => 'Inquiries or issues related to international relations and consular services.',
                'configuration' => json_encode(['allow_visa_application' => true, 'allow_diplomatic_engagement' => true]),
            ],
            [
                'name' => 'Disaster Management',
                'slug' => Str::slug('Disaster Management'),
                'description' => 'Inquiries or issues related to disaster response and emergency preparedness.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_resource_allocation' => true]),
            ],
            [
                'name' => 'Public Works',
                'slug' => Str::slug('Public Works'),
                'description' => 'Inquiries or issues related to public infrastructure and construction projects.',
                'configuration' => json_encode(['allow_project_proposals' => true, 'allow_progress_tracking' => true]),
            ],
        ];

        // Insert the ticket types into the database using Eloquent
        foreach ($types as $key => $type) {
            TicketType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}