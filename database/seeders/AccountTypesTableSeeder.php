<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\AccountType;

class AccountTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the account_types table
        \DB::table('account_types')->delete();

        // Define e-government account types
        $types = [
            [
                'name' => 'Revenue Account',
                'slug' => Str::slug('Revenue Account'),
                'description' => 'Accounts used to track income generated from taxes, fees, and other sources.',
                'configuration' => json_encode(['allow_income_tracking' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Expense Account',
                'slug' => Str::slug('Expense Account'),
                'description' => 'Accounts used to track government expenditures and operational costs.',
                'configuration' => json_encode(['allow_expense_tracking' => true, 'allow_budget_allocation' => true]),
            ],
            [
                'name' => 'Asset Account',
                'slug' => Str::slug('Asset Account'),
                'description' => 'Accounts used to track government-owned assets such as property, equipment, and investments.',
                'configuration' => json_encode(['allow_asset_management' => true, 'allow_depreciation_tracking' => true]),
            ],
            [
                'name' => 'Liability Account',
                'slug' => Str::slug('Liability Account'),
                'description' => 'Accounts used to track government liabilities such as loans, bonds, and payables.',
                'configuration' => json_encode(['allow_liability_tracking' => true, 'allow_payment_scheduling' => true]),
            ],
            [
                'name' => 'Equity Account',
                'slug' => Str::slug('Equity Account'),
                'description' => 'Accounts used to track government equity, including reserves and retained earnings.',
                'configuration' => json_encode(['allow_equity_tracking' => true, 'allow_dividend_distribution' => true]),
            ],
            [
                'name' => 'Tax Account',
                'slug' => Str::slug('Tax Account'),
                'description' => 'Accounts used to track tax revenues and related transactions.',
                'configuration' => json_encode(['allow_tax_collection' => true, 'allow_tax_refunds' => true]),
            ],
            [
                'name' => 'Grant Account',
                'slug' => Str::slug('Grant Account'),
                'description' => 'Accounts used to track grants received and disbursed by the government.',
                'configuration' => json_encode(['allow_grant_management' => true, 'allow_funding_disbursement' => true]),
            ],
            [
                'name' => 'Donation Account',
                'slug' => Str::slug('Donation Account'),
                'description' => 'Accounts used to track donations received from individuals, organizations, or other entities.',
                'configuration' => json_encode(['allow_donation_tracking' => true, 'allow_tax_deduction' => true]),
            ],
            [
                'name' => 'Loan Account',
                'slug' => Str::slug('Loan Account'),
                'description' => 'Accounts used to track loans issued or received by the government.',
                'configuration' => json_encode(['allow_loan_management' => true, 'allow_repayment_scheduling' => true]),
            ],
            [
                'name' => 'Budget Account',
                'slug' => Str::slug('Budget Account'),
                'description' => 'Accounts used to track budget allocations and expenditures.',
                'configuration' => json_encode(['allow_budget_tracking' => true, 'allow_fund_allocation' => true]),
            ],
            [
                'name' => 'Reserve Account',
                'slug' => Str::slug('Reserve Account'),
                'description' => 'Accounts used to track reserve funds set aside for emergencies or future projects.',
                'configuration' => json_encode(['allow_reserve_management' => true, 'allow_fund_withdrawal' => true]),
            ],
            [
                'name' => 'Pension Account',
                'slug' => Str::slug('Pension Account'),
                'description' => 'Accounts used to track pension funds and related transactions.',
                'configuration' => json_encode(['allow_pension_management' => true, 'allow_benefit_disbursement' => true]),
            ],
            [
                'name' => 'Investment Account',
                'slug' => Str::slug('Investment Account'),
                'description' => 'Accounts used to track government investments in projects, stocks, or other assets.',
                'configuration' => json_encode(['allow_investment_tracking' => true, 'allow_returns_tracking' => true]),
            ],
            [
                'name' => 'Trust Account',
                'slug' => Str::slug('Trust Account'),
                'description' => 'Accounts used to track funds held in trust for specific purposes or beneficiaries.',
                'configuration' => json_encode(['allow_trust_management' => true, 'allow_fund_disbursement' => true]),
            ],
            [
                'name' => 'Clearing Account',
                'slug' => Str::slug('Clearing Account'),
                'description' => 'Accounts used to temporarily hold funds during the processing of transactions.',
                'configuration' => json_encode(['allow_clearing_management' => true, 'allow_fund_transfer' => true]),
            ],
            [
                'name' => 'Contingency Account',
                'slug' => Str::slug('Contingency Account'),
                'description' => 'Accounts used to track funds set aside for unforeseen expenses or emergencies.',
                'configuration' => json_encode(['allow_contingency_management' => true, 'allow_fund_withdrawal' => true]),
            ],
            [
                'name' => 'Capital Account',
                'slug' => Str::slug('Capital Account'),
                'description' => 'Accounts used to track capital investments and infrastructure projects.',
                'configuration' => json_encode(['allow_capital_tracking' => true, 'allow_project_funding' => true]),
            ],
            [
                'name' => 'Operating Account',
                'slug' => Str::slug('Operating Account'),
                'description' => 'Accounts used to track day-to-day operational expenses and revenues.',
                'configuration' => json_encode(['allow_operational_tracking' => true, 'allow_budget_allocation' => true]),
            ],
            [
                'name' => 'Special Funds Account',
                'slug' => Str::slug('Special Funds Account'),
                'description' => 'Accounts used to track funds allocated for specific programs or initiatives.',
                'configuration' => json_encode(['allow_fund_management' => true, 'allow_fund_disbursement' => true]),
            ],
            [
                'name' => 'Foreign Exchange Account',
                'slug' => Str::slug('Foreign Exchange Account'),
                'description' => 'Accounts used to track foreign currency transactions and exchange rates.',
                'configuration' => json_encode(['allow_currency_tracking' => true, 'allow_exchange_rate_management' => true]),
            ],
        ];

        // Insert the account types into the database using Eloquent
        foreach ($types as $key => $type) {
            AccountType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}