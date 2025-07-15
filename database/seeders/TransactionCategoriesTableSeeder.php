<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\TransactionCategory;

class TransactionCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the transaction_categories table
        \DB::table('transaction_categories')->delete();

        // Define e-government transaction categories
        $categories = [
            [
                'name' => 'Tax Payments',
                'slug' => Str::slug('Tax Payments'),
                'description' => 'Transactions related to tax payments and revenue collection.',
                'configuration' => json_encode(['allow_online_payment' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Utility Bills',
                'slug' => Str::slug('Utility Bills'),
                'description' => 'Transactions related to payments for utilities such as electricity, water, and gas.',
                'configuration' => json_encode(['allow_recurring_payments' => true, 'allow_bill_upload' => true]),
            ],
            [
                'name' => 'License and Permit Fees',
                'slug' => Str::slug('License and Permit Fees'),
                'description' => 'Transactions related to payments for licenses, permits, and certifications.',
                'configuration' => json_encode(['allow_online_application' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Fines and Penalties',
                'slug' => Str::slug('Fines and Penalties'),
                'description' => 'Transactions related to payments for fines, penalties, and legal fees.',
                'configuration' => json_encode(['allow_dispute_submission' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Social Welfare Payments',
                'slug' => Str::slug('Social Welfare Payments'),
                'description' => 'Transactions related to social welfare benefits and assistance programs.',
                'configuration' => json_encode(['allow_benefit_application' => true, 'allow_direct_deposit' => true]),
            ],
            [
                'name' => 'Healthcare Payments',
                'slug' => Str::slug('Healthcare Payments'),
                'description' => 'Transactions related to payments for healthcare services and medical bills.',
                'configuration' => json_encode(['allow_insurance_claims' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Education Fees',
                'slug' => Str::slug('Education Fees'),
                'description' => 'Transactions related to payments for tuition, school fees, and educational programs.',
                'configuration' => json_encode(['allow_scholarship_application' => true, 'allow_installment_payments' => true]),
            ],
            [
                'name' => 'Public Transportation Fees',
                'slug' => Str::slug('Public Transportation Fees'),
                'description' => 'Transactions related to payments for public transportation services.',
                'configuration' => json_encode(['allow_monthly_passes' => true, 'allow_auto_renewal' => true]),
            ],
            [
                'name' => 'Tourism and Recreation Fees',
                'slug' => Str::slug('Tourism and Recreation Fees'),
                'description' => 'Transactions related to payments for tourism and recreational activities.',
                'configuration' => json_encode(['allow_event_registration' => true, 'allow_refunds' => true]),
            ],
            [
                'name' => 'Business and Trade Fees',
                'slug' => Str::slug('Business and Trade Fees'),
                'description' => 'Transactions related to payments for business registrations, trade permits, and commercial fees.',
                'configuration' => json_encode(['allow_online_registration' => true, 'allow_bulk_payments' => true]),
            ],
            [
                'name' => 'Housing and Property Fees',
                'slug' => Str::slug('Housing and Property Fees'),
                'description' => 'Transactions related to payments for housing, property taxes, and development fees.',
                'configuration' => json_encode(['allow_property_registration' => true, 'allow_installment_payments' => true]),
            ],
            [
                'name' => 'Immigration and Visa Fees',
                'slug' => Str::slug('Immigration and Visa Fees'),
                'description' => 'Transactions related to payments for immigration services and visa applications.',
                'configuration' => json_encode(['allow_online_application' => true, 'allow_status_tracking' => true]),
            ],
            [
                'name' => 'Legal and Court Fees',
                'slug' => Str::slug('Legal and Court Fees'),
                'description' => 'Transactions related to payments for legal services and court fees.',
                'configuration' => json_encode(['allow_case_submission' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Environmental Fees',
                'slug' => Str::slug('Environmental Fees'),
                'description' => 'Transactions related to payments for environmental permits and sustainability programs.',
                'configuration' => json_encode(['allow_public_participation' => true, 'allow_document_upload' => true]),
            ],
            [
                'name' => 'Energy and Resource Fees',
                'slug' => Str::slug('Energy and Resource Fees'),
                'description' => 'Transactions related to payments for energy resources and utility services.',
                'configuration' => json_encode(['allow_service_requests' => true, 'allow_billing_information' => true]),
            ],
            [
                'name' => 'Digital Services Fees',
                'slug' => Str::slug('Digital Services Fees'),
                'description' => 'Transactions related to payments for digital services and e-governance platforms.',
                'configuration' => json_encode(['allow_innovation_proposals' => true, 'allow_technology_adoption' => true]),
            ],
            [
                'name' => 'Labor and Employment Fees',
                'slug' => Str::slug('Labor and Employment Fees'),
                'description' => 'Transactions related to payments for labor regulations and employment services.',
                'configuration' => json_encode(['allow_job_application' => true, 'allow_training_enrollment' => true]),
            ],
            [
                'name' => 'Foreign Affairs Fees',
                'slug' => Str::slug('Foreign Affairs Fees'),
                'description' => 'Transactions related to payments for international relations and consular services.',
                'configuration' => json_encode(['allow_visa_application' => true, 'allow_diplomatic_engagement' => true]),
            ],
            [
                'name' => 'Disaster Management Fees',
                'slug' => Str::slug('Disaster Management Fees'),
                'description' => 'Transactions related to payments for disaster response and emergency preparedness.',
                'configuration' => json_encode(['allow_emergency_contact' => true, 'allow_resource_allocation' => true]),
            ],
            [
                'name' => 'Public Works Fees',
                'slug' => Str::slug('Public Works Fees'),
                'description' => 'Transactions related to payments for public infrastructure and construction projects.',
                'configuration' => json_encode(['allow_project_proposals' => true, 'allow_progress_tracking' => true]),
            ],
        ];

        // Insert the transaction categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            TransactionCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}