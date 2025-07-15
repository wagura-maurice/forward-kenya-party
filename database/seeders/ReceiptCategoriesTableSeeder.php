<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ReceiptCategory;

class ReceiptCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the receipt_categories table
        \DB::table('receipt_categories')->delete();

        // Define e-government receipt categories
        $categories = [
            [
                'name' => 'Payment Receipt',
                'slug' => Str::slug('Payment Receipt'),
                'description' => 'Receipts issued for payments made to the government.',
                'configuration' => json_encode(['allow_payment_tracking' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Tax Receipt',
                'slug' => Str::slug('Tax Receipt'),
                'description' => 'Receipts issued for tax payments made to the government.',
                'configuration' => json_encode(['allow_tax_calculation' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Utility Receipt',
                'slug' => Str::slug('Utility Receipt'),
                'description' => 'Receipts issued for utility payments such as electricity, water, and gas.',
                'configuration' => json_encode(['allow_recurring_billing' => true, 'allow_bill_upload' => true]),
            ],
            [
                'name' => 'License and Permit Receipt',
                'slug' => Str::slug('License and Permit Receipt'),
                'description' => 'Receipts issued for payments related to licenses, permits, and certifications.',
                'configuration' => json_encode(['allow_online_application' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Fine and Penalty Receipt',
                'slug' => Str::slug('Fine and Penalty Receipt'),
                'description' => 'Receipts issued for payments related to fines, penalties, and legal fees.',
                'configuration' => json_encode(['allow_dispute_submission' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Service Receipt',
                'slug' => Str::slug('Service Receipt'),
                'description' => 'Receipts issued for payments related to government services provided to citizens or businesses.',
                'configuration' => json_encode(['allow_service_selection' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Healthcare Receipt',
                'slug' => Str::slug('Healthcare Receipt'),
                'description' => 'Receipts issued for payments related to healthcare services and medical bills.',
                'configuration' => json_encode(['allow_insurance_claims' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Education Receipt',
                'slug' => Str::slug('Education Receipt'),
                'description' => 'Receipts issued for payments related to tuition, school fees, and educational programs.',
                'configuration' => json_encode(['allow_scholarship_application' => true, 'allow_installment_payments' => true]),
            ],
            [
                'name' => 'Transportation Receipt',
                'slug' => Str::slug('Transportation Receipt'),
                'description' => 'Receipts issued for payments related to public transportation services.',
                'configuration' => json_encode(['allow_monthly_passes' => true, 'allow_auto_renewal' => true]),
            ],
            [
                'name' => 'Tourism and Recreation Receipt',
                'slug' => Str::slug('Tourism and Recreation Receipt'),
                'description' => 'Receipts issued for payments related to tourism and recreational activities.',
                'configuration' => json_encode(['allow_event_registration' => true, 'allow_refunds' => true]),
            ],
            [
                'name' => 'Business and Trade Receipt',
                'slug' => Str::slug('Business and Trade Receipt'),
                'description' => 'Receipts issued for payments related to business registrations, trade permits, and commercial fees.',
                'configuration' => json_encode(['allow_online_registration' => true, 'allow_bulk_payments' => true]),
            ],
            [
                'name' => 'Housing and Property Receipt',
                'slug' => Str::slug('Housing and Property Receipt'),
                'description' => 'Receipts issued for payments related to housing, property taxes, and development fees.',
                'configuration' => json_encode(['allow_property_registration' => true, 'allow_installment_payments' => true]),
            ],
            [
                'name' => 'Immigration and Visa Receipt',
                'slug' => Str::slug('Immigration and Visa Receipt'),
                'description' => 'Receipts issued for payments related to immigration services and visa applications.',
                'configuration' => json_encode(['allow_online_application' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Legal and Court Receipt',
                'slug' => Str::slug('Legal and Court Receipt'),
                'description' => 'Receipts issued for payments related to legal services and court fees.',
                'configuration' => json_encode(['allow_case_submission' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Environmental Receipt',
                'slug' => Str::slug('Environmental Receipt'),
                'description' => 'Receipts issued for payments related to environmental permits and sustainability programs.',
                'configuration' => json_encode(['allow_public_participation' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Energy and Resource Receipt',
                'slug' => Str::slug('Energy and Resource Receipt'),
                'description' => 'Receipts issued for payments related to energy resources and utility services.',
                'configuration' => json_encode(['allow_service_requests' => true, 'allow_billing_information' => true]),
            ],
            [
                'name' => 'Digital Services Receipt',
                'slug' => Str::slug('Digital Services Receipt'),
                'description' => 'Receipts issued for payments related to digital services and e-governance platforms.',
                'configuration' => json_encode(['allow_innovation_proposals' => true, 'allow_technology_adoption' => true]),
            ],
            [
                'name' => 'Labor and Employment Receipt',
                'slug' => Str::slug('Labor and Employment Receipt'),
                'description' => 'Receipts issued for payments related to labor regulations and employment services.',
                'configuration' => json_encode(['allow_job_application' => true, 'allow_training_enrollment' => true]),
            ],
            [
                'name' => 'Foreign Affairs Receipt',
                'slug' => Str::slug('Foreign Affairs Receipt'),
                'description' => 'Receipts issued for payments related to international relations and consular services.',
                'configuration' => json_encode(['allow_visa_application' => true, 'allow_diplomatic_engagement' => true]),
            ],
            [
                'name' => 'Disaster Management Receipt',
                'slug' => Str::slug('Disaster Management Receipt'),
                'description' => 'Receipts issued for payments related to disaster response and emergency preparedness.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_resource_allocation' => true]),
            ],
            [
                'name' => 'Public Works Receipt',
                'slug' => Str::slug('Public Works Receipt'),
                'description' => 'Receipts issued for payments related to public infrastructure and construction projects.',
                'configuration' => json_encode(['allow_project_proposals' => true, 'allow_progress_tracking' => true]),
            ],
        ];

        // Insert the receipt categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            ReceiptCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}