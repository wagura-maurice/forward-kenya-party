<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\AccountCategory;

class AccountCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the account_categories table
        \DB::table('account_categories')->delete();

        // Define e-government account categories
        $categories = [
            [
                'name' => 'Revenue Accounts',
                'slug' => Str::slug('Revenue Accounts'),
                'description' => 'Accounts used to track income generated from taxes, fees, and other sources.',
                'configuration' => json_encode(['allow_income_tracking' => true, 'allow_receipt_generation' => true]),
            ],
            [
                'name' => 'Expense Accounts',
                'slug' => Str::slug('Expense Accounts'),
                'description' => 'Accounts used to track government expenditures and operational costs.',
                'configuration' => json_encode(['allow_expense_tracking' => true, 'allow_budget_allocation' => true]),
            ],
            [
                'name' => 'Asset Accounts',
                'slug' => Str::slug('Asset Accounts'),
                'description' => 'Accounts used to track government-owned assets such as property, equipment, and investments.',
                'configuration' => json_encode(['allow_asset_management' => true, 'allow_depreciation_tracking' => true]),
            ],
            [
                'name' => 'Liability Accounts',
                'slug' => Str::slug('Liability Accounts'),
                'description' => 'Accounts used to track government liabilities such as loans, bonds, and payables.',
                'configuration' => json_encode(['allow_liability_tracking' => true, 'allow_payment_scheduling' => true]),
            ],
            [
                'name' => 'Equity Accounts',
                'slug' => Str::slug('Equity Accounts'),
                'description' => 'Accounts used to track government equity, including reserves and retained earnings.',
                'configuration' => json_encode(['allow_equity_tracking' => true, 'allow_dividend_distribution' => true]),
            ],
            [
                'name' => 'Tax Accounts',
                'slug' => Str::slug('Tax Accounts'),
                'description' => 'Accounts used to track tax revenues and related transactions.',
                'configuration' => json_encode(['allow_tax_collection' => true, 'allow_tax_refunds' => true]),
            ],
            [
                'name' => 'Grant Accounts',
                'slug' => Str::slug('Grant Accounts'),
                'description' => 'Accounts used to track grants received and disbursed by the government.',
                'configuration' => json_encode(['allow_grant_management' => true, 'allow_funding_disbursement' => true]),
            ],
            [
                'name' => 'Donation Accounts',
                'slug' => Str::slug('Donation Accounts'),
                'description' => 'Accounts used to track donations received from individuals, organizations, or other entities.',
                'configuration' => json_encode(['allow_donation_tracking' => true, 'allow_tax_deduction' => true]),
            ],
            [
                'name' => 'Loan Accounts',
                'slug' => Str::slug('Loan Accounts'),
                'description' => 'Accounts used to track loans issued or received by the government.',
                'configuration' => json_encode(['allow_loan_management' => true, 'allow_repayment_scheduling' => true]),
            ],
            [
                'name' => 'Budget Accounts',
                'slug' => Str::slug('Budget Accounts'),
                'description' => 'Accounts used to track budget allocations and expenditures.',
                'configuration' => json_encode(['allow_budget_tracking' => true, 'allow_fund_allocation' => true]),
            ],
            [
                'name' => 'Reserve Accounts',
                'slug' => Str::slug('Reserve Accounts'),
                'description' => 'Accounts used to track reserve funds set aside for emergencies or future projects.',
                'configuration' => json_encode(['allow_reserve_management' => true, 'allow_fund_withdrawal' => true]),
            ],
            [
                'name' => 'Pension Accounts',
                'slug' => Str::slug('Pension Accounts'),
                'description' => 'Accounts used to track pension funds and related transactions.',
                'configuration' => json_encode(['allow_pension_management' => true, 'allow_benefit_disbursement' => true]),
            ],
            [
                'name' => 'Investment Accounts',
                'slug' => Str::slug('Investment Accounts'),
                'description' => 'Accounts used to track government investments in projects, stocks, or other assets.',
                'configuration' => json_encode(['allow_investment_tracking' => true, 'allow_returns_tracking' => true]),
            ],
            [
                'name' => 'Trust Accounts',
                'slug' => Str::slug('Trust Accounts'),
                'description' => 'Accounts used to track funds held in trust for specific purposes or beneficiaries.',
                'configuration' => json_encode(['allow_trust_management' => true, 'allow_fund_disbursement' => true]),
            ],
            [
                'name' => 'Clearing Accounts',
                'slug' => Str::slug('Clearing Accounts'),
                'description' => 'Accounts used to temporarily hold funds during the processing of transactions.',
                'configuration' => json_encode(['allow_clearing_management' => true, 'allow_fund_transfer' => true]),
            ],
            [
                'name' => 'Contingency Accounts',
                'slug' => Str::slug('Contingency Accounts'),
                'description' => 'Accounts used to track funds set aside for unforeseen expenses or emergencies.',
                'configuration' => json_encode(['allow_contingency_management' => true, 'allow_fund_withdrawal' => true]),
            ],
            [
                'name' => 'Capital Accounts',
                'slug' => Str::slug('Capital Accounts'),
                'description' => 'Accounts used to track capital investments and infrastructure projects.',
                'configuration' => json_encode(['allow_capital_tracking' => true, 'allow_project_funding' => true]),
            ],
            [
                'name' => 'Operating Accounts',
                'slug' => Str::slug('Operating Accounts'),
                'description' => 'Accounts used to track day-to-day operational expenses and revenues.',
                'configuration' => json_encode(['allow_operational_tracking' => true, 'allow_budget_allocation' => true]),
            ],
            [
                'name' => 'Special Funds Accounts',
                'slug' => Str::slug('Special Funds Accounts'),
                'description' => 'Accounts used to track funds allocated for specific programs or initiatives.',
                'configuration' => json_encode(['allow_fund_management' => true, 'allow_fund_disbursement' => true]),
            ],
            [
                'name' => 'Foreign Exchange Accounts',
                'slug' => Str::slug('Foreign Exchange Accounts'),
                'description' => 'Accounts used to track foreign currency transactions and exchange rates.',
                'configuration' => json_encode(['allow_currency_tracking' => true, 'allow_exchange_rate_management' => true]),
            ],
        ];

        // Insert the account categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            AccountCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}