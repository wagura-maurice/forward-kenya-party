<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\TransactionType;

class TransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the transaction_types table
        \DB::table('transaction_types')->delete();

        // Define e-government transaction types
        $types = [
            [
                'name' => 'Payment',
                'slug' => Str::slug('Payment'),
                'description' => 'Transactions involving payments for services, fees, or taxes.',
                'configuration' => json_encode(['allow_online_payment' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Refund',
                'slug' => Str::slug('Refund'),
                'description' => 'Transactions involving refunds for overpayments or canceled services.',
                'configuration' => json_encode(['allow_refund_requests' => true, 'allow_auto_processing' => true]),
            ],
            [
                'name' => 'Donation',
                'slug' => Str::slug('Donation'),
                'description' => 'Transactions involving donations to government programs or initiatives.',
                'configuration' => json_encode(['allow_recurring_donations' => true, 'allow_tax_deduction' => true]),
            ],
            [
                'name' => 'Grant',
                'slug' => Str::slug('Grant'),
                'description' => 'Transactions involving grants for projects, research, or community initiatives.',
                'configuration' => json_encode(['allow_application_submission' => true, 'allow_funding_disbursement' => true]),
            ],
            [
                'name' => 'Loan',
                'slug' => Str::slug('Loan'),
                'description' => 'Transactions involving loans provided by the government to citizens or businesses.',
                'configuration' => json_encode(['allow_loan_application' => true, 'allow_repayment_scheduling' => true]),
            ],
            [
                'name' => 'Fine',
                'slug' => Str::slug('Fine'),
                'description' => 'Transactions involving fines or penalties for violations or non-compliance.',
                'configuration' => json_encode(['allow_dispute_submission' => true, 'allow_payment_plans' => true]),
            ],
            [
                'name' => 'Subscription',
                'slug' => Str::slug('Subscription'),
                'description' => 'Transactions involving subscriptions for government services or publications.',
                'configuration' => json_encode(['allow_auto_renewal' => true, 'allow_cancellation' => true]),
            ],
            [
                'name' => 'Reimbursement',
                'slug' => Str::slug('Reimbursement'),
                'description' => 'Transactions involving reimbursements for expenses incurred by citizens or businesses.',
                'configuration' => json_encode(['allow_expense_submission' => true, 'allow_auto_processing' => true]),
            ],
            [
                'name' => 'Transfer',
                'slug' => Str::slug('Transfer'),
                'description' => 'Transactions involving transfers of funds between government accounts or to citizens.',
                'configuration' => json_encode(['allow_inter_account_transfers' => true, 'allow_external_transfers' => true]),
            ],
            [
                'name' => 'Investment',
                'slug' => Str::slug('Investment'),
                'description' => 'Transactions involving investments in government projects or initiatives.',
                'configuration' => json_encode(['allow_investment_proposals' => true, 'allow_returns_tracking' => true]),
            ],
            [
                'name' => 'Award',
                'slug' => Str::slug('Award'),
                'description' => 'Transactions involving awards or prizes for achievements or competitions.',
                'configuration' => json_encode(['allow_nomination_submission' => true, 'allow_funding_disbursement' => true]),
            ],
            [
                'name' => 'Scholarship',
                'slug' => Str::slug('Scholarship'),
                'description' => 'Transactions involving scholarships for education or training programs.',
                'configuration' => json_encode(['allow_application_submission' => true, 'allow_funding_disbursement' => true]),
            ],
            [
                'name' => 'Insurance',
                'slug' => Str::slug('Insurance'),
                'description' => 'Transactions involving insurance payments or claims.',
                'configuration' => json_encode(['allow_premium_payments' => true, 'allow_claims_submission' => true]),
            ],
            [
                'name' => 'Pension',
                'slug' => Str::slug('Pension'),
                'description' => 'Transactions involving pension payments or contributions.',
                'configuration' => json_encode(['allow_contributions' => true, 'allow_benefit_disbursement' => true]),
            ],
            [
                'name' => 'Royalty',
                'slug' => Str::slug('Royalty'),
                'description' => 'Transactions involving royalties for the use of government-owned resources or intellectual property.',
                'configuration' => json_encode(['allow_royalty_calculation' => true, 'allow_payment_scheduling' => true]),
            ],
            [
                'name' => 'Sponsorship',
                'slug' => Str::slug('Sponsorship'),
                'description' => 'Transactions involving sponsorships for events, programs, or initiatives.',
                'configuration' => json_encode(['allow_sponsorship_requests' => true, 'allow_funding_disbursement' => true]),
            ],
            [
                'name' => 'Compensation',
                'slug' => Str::slug('Compensation'),
                'description' => 'Transactions involving compensation for damages, losses, or services rendered.',
                'configuration' => json_encode(['allow_claim_submission' => true, 'allow_auto_processing' => true]),
            ],
            [
                'name' => 'Revenue Collection',
                'slug' => Str::slug('Revenue Collection'),
                'description' => 'Transactions involving the collection of revenue from taxes, fees, or other sources.',
                'configuration' => json_encode(['allow_online_payment' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Expense',
                'slug' => Str::slug('Expense'),
                'description' => 'Transactions involving expenses incurred by the government.',
                'configuration' => json_encode(['allow_expense_submission' => true, 'allow_approval_workflow' => true]),
            ],
            [
                'name' => 'Budget Allocation',
                'slug' => Str::slug('Budget Allocation'),
                'description' => 'Transactions involving the allocation of budgets to government departments or projects.',
                'configuration' => json_encode(['allow_budget_proposals' => true, 'allow_funding_disbursement' => true]),
            ],
        ];

        // Insert the transaction types into the database using Eloquent
        foreach ($types as $key => $type) {
            TransactionType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}