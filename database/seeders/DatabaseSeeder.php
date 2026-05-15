<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
        
        // Media and document management
        $this->call(MediaTypesTableSeeder::class);
        $this->call(MediaCategoriesTableSeeder::class);
        $this->call(DocumentTypesTableSeeder::class);
        $this->call(DocumentCategoriesTableSeeder::class);
        
        // Core system tables (verified to exist in migrations) - must run first
        $this->call(SettingsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(AbilitiesTableSeeder::class);
        
        // Currency system
        $this->call(CurrencyTypesTableSeeder::class);
        $this->call(CurrencyCategoriesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(ExchangeRatesTableSeeder::class);
        
        // Geographic hierarchy (Kenya-specific)
        $this->call(CountiesTableSeeder::class);
        $this->call(SubCountiesTableSeeder::class);
        $this->call(ConstituenciesTableSeeder::class);
        $this->call(WardsTableSeeder::class);
        $this->call(LocationsTableSeeder::class);
        $this->call(VillagesTableSeeder::class);
        
        // Polling system (user requested to keep these)
        $this->call(PollingCenterTypeSeeder::class);
        $this->call(PollingCenterCategorySeeder::class);
        $this->call(PollingCenterSeeder::class);
        $this->call(PollingStationTypeSeeder::class);
        $this->call(PollingStationCategorySeeder::class);
        $this->call(PollingStationsTableSeeder::class);
        $this->call(PollingStreamTypeSeeder::class);
        $this->call(PollingStreamCategorySeeder::class);
        $this->call(PollingStreamSeeder::class);
        
        // Banking system
        $this->call(BankTypesTableSeeder::class);
        $this->call(BankCategoriesTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        
        // Ethnicity and religion systems
        $this->call(EthnicityCategoryTableSeeder::class);
        $this->call(EthnicityTypeSeeder::class);
        $this->call(EthnicityTableSeeder::class);
        $this->call(ReligionTypeTableSeeder::class);
        $this->call(ReligionCategoryTableSeeder::class);
        $this->call(ReligionTableSeeder::class);
        
        // Language system
        $this->call(LanguageCategoryTableSeeder::class);
        $this->call(LanguageTypeTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        
        // Department and service systems (verified to exist in migrations)
        $this->call(DepartmentTypesTableSeeder::class);
        $this->call(DepartmentCategoriesTableSeeder::class);
        $this->call(DepartmentsTableSeeder::class);
        $this->call(ServiceTypesTableSeeder::class);
        $this->call(ServiceCategoriesTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
        $this->call(DepartmentServiceTableSeeder::class);
        
        // Activity and notification systems (verified to exist in migrations)
        $this->call(ActivityTypesTableSeeder::class);
        $this->call(ActivityCategoriesTableSeeder::class);
        
        // Exchange rate system (verified to exist in migrations)
        $this->call(ExchangeRatesTableSeeder::class);
        
        // Account and transaction systems (verified to exist in migrations)
        $this->call(AccountTypesTableSeeder::class);
        $this->call(AccountCategoriesTableSeeder::class);
        $this->call(ReceiptTypesTableSeeder::class);
        $this->call(ReceiptCategoriesTableSeeder::class);
        $this->call(InvoiceTypesTableSeeder::class);
        $this->call(InvoiceCategoriesTableSeeder::class);
        $this->call(TransactionTypesTableSeeder::class);
        $this->call(TransactionCategoriesTableSeeder::class);
        
        // Feedback and ticket systems (verified to exist in migrations)
        $this->call(FeedbackTypesTableSeeder::class);
        $this->call(FeedbackCategoriesTableSeeder::class);
        $this->call(TicketTypesTableSeeder::class);
        $this->call(TicketCategoriesTableSeeder::class);
        
        // Communication systems (verified to exist in migrations)
        $this->call(CommunicationTypesTableSeeder::class);
        $this->call(CommunicationCategoriesTableSeeder::class);
        
        // Create default users with role assignments
        // create default administrator
        User::factory()->create([
            'name' => 'administrator',
            'email' => 'administrator@forwardkenyaparty.com',
        ])->assignRole('administrator');

        // create default manager
        User::factory()->create([
            'name' => 'manager',
            'email' => 'manager@forwardkenyaparty.com',
        ])->assignRole('manager');

        // create default member
        User::factory()->create([
            'name' => 'member',
            'email' => 'member@forwardkenyaparty.com',
        ])->assignRole('member');

        // create default guest
        User::factory()->create([
            'name' => 'guest',
            'email' => 'guest@forwardkenyaparty.com',
        ])->assignRole('guest');
    }
}
