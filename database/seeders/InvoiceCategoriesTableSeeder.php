<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\InvoiceCategory;

class InvoiceCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the invoice_categories table
        \DB::table('invoice_categories')->delete();

        // Define e-government invoice categories
        $categories = [
            [
                'name' => 'Tax Invoice',
                'slug' => Str::slug('Tax Invoice'),
                'description' => 'Invoices related to tax payments and revenue collection.',
                'configuration' => json_encode(['allow_tax_calculation' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Utility Invoice',
                'slug' => Str::slug('Utility Invoice'),
                'description' => 'Invoices related to payments for utilities such as electricity, water, and gas.',
                'configuration' => json_encode(['allow_recurring_billing' => true, 'allow_bill_upload' => true]),
            ],
            [
                'name' => 'License and Permit Invoice',
                'slug' => Str::slug('License and Permit Invoice'),
                'description' => 'Invoices related to payments for licenses, permits, and certifications.',
                'configuration' => json_encode(['allow_online_application' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Fine and Penalty Invoice',
                'slug' => Str::slug('Fine and Penalty Invoice'),
                'description' => 'Invoices related to payments for fines, penalties, and legal fees.',
                'configuration' => json_encode(['allow_dispute_submission' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Service Invoice',
                'slug' => Str::slug('Service Invoice'),
                'description' => 'Invoices related to payments for government services provided to citizens or businesses.',
                'configuration' => json_encode(['allow_service_selection' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Healthcare Invoice',
                'slug' => Str::slug('Healthcare Invoice'),
                'description' => 'Invoices related to payments for healthcare services and medical bills.',
                'configuration' => json_encode(['allow_insurance_claims' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Education Invoice',
                'slug' => Str::slug('Education Invoice'),
                'description' => 'Invoices related to payments for tuition, school fees, and educational programs.',
                'configuration' => json_encode(['allow_scholarship_application' => true, 'allow_installment_payments' => true]),
            ],
            [
                'name' => 'Transportation Invoice',
                'slug' => Str::slug('Transportation Invoice'),
                'description' => 'Invoices related to payments for public transportation services.',
                'configuration' => json_encode(['allow_monthly_passes' => true, 'allow_auto_renewal' => true]),
            ],
            [
                'name' => 'Tourism and Recreation Invoice',
                'slug' => Str::slug('Tourism and Recreation Invoice'),
                'description' => 'Invoices related to payments for tourism and recreational activities.',
                'configuration' => json_encode(['allow_event_registration' => true, 'allow_refunds' => true]),
            ],
            [
                'name' => 'Business and Trade Invoice',
                'slug' => Str::slug('Business and Trade Invoice'),
                'description' => 'Invoices related to payments for business registrations, trade permits, and commercial fees.',
                'configuration' => json_encode(['allow_online_registration' => true, 'allow_bulk_payments' => true]),
            ],
            [
                'name' => 'Housing and Property Invoice',
                'slug' => Str::slug('Housing and Property Invoice'),
                'description' => 'Invoices related to payments for housing, property taxes, and development fees.',
                'configuration' => json_encode(['allow_property_registration' => true, 'allow_installment_payments' => true]),
            ],
            [
                'name' => 'Immigration and Visa Invoice',
                'slug' => Str::slug('Immigration and Visa Invoice'),
                'description' => 'Invoices related to payments for immigration services and visa applications.',
                'configuration' => json_encode(['allow_online_application' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Legal and Court Invoice',
                'slug' => Str::slug('Legal and Court Invoice'),
                'description' => 'Invoices related to payments for legal services and court fees.',
                'configuration' => json_encode(['allow_case_submission' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Environmental Invoice',
                'slug' => Str::slug('Environmental Invoice'),
                'description' => 'Invoices related to payments for environmental permits and sustainability programs.',
                'configuration' => json_encode(['allow_public_participation' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Energy and Resource Invoice',
                'slug' => Str::slug('Energy and Resource Invoice'),
                'description' => 'Invoices related to payments for energy resources and utility services.',
                'configuration' => json_encode(['allow_service_requests' => true, 'allow_billing_information' => true]),
            ],
            [
                'name' => 'Digital Services Invoice',
                'slug' => Str::slug('Digital Services Invoice'),
                'description' => 'Invoices related to payments for digital services and e-governance platforms.',
                'configuration' => json_encode(['allow_innovation_proposals' => true, 'allow_technology_adoption' => true]),
            ],
            [
                'name' => 'Labor and Employment Invoice',
                'slug' => Str::slug('Labor and Employment Invoice'),
                'description' => 'Invoices related to payments for labor regulations and employment services.',
                'configuration' => json_encode(['allow_job_application' => true, 'allow_training_enrollment' => true]),
            ],
            [
                'name' => 'Foreign Affairs Invoice',
                'slug' => Str::slug('Foreign Affairs Invoice'),
                'description' => 'Invoices related to payments for international relations and consular services.',
                'configuration' => json_encode(['allow_visa_application' => true, 'allow_diplomatic_engagement' => true]),
            ],
            [
                'name' => 'Disaster Management Invoice',
                'slug' => Str::slug('Disaster Management Invoice'),
                'description' => 'Invoices related to payments for disaster response and emergency preparedness.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_resource_allocation' => true]),
            ],
            [
                'name' => 'Public Works Invoice',
                'slug' => Str::slug('Public Works Invoice'),
                'description' => 'Invoices related to payments for public infrastructure and construction projects.',
                'configuration' => json_encode(['allow_project_proposals' => true, 'allow_progress_tracking' => true]),
            ],
        ];

        // Insert the invoice categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            InvoiceCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}