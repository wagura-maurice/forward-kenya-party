<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        
        $this->call(MediaTypesTableSeeder::class);
        $this->call(MediaCategoriesTableSeeder::class);
        $this->call(DocumentTypesTableSeeder::class);
        $this->call(DocumentCategoriesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(AbilitiesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(CountiesTableSeeder::class);
        $this->call(SubCountiesTableSeeder::class);
        $this->call(ConstituenciesTableSeeder::class);
        $this->call(WardsTableSeeder::class);
        $this->call(LocationsTableSeeder::class);
        $this->call(VillagesTableSeeder::class);
        // $this->call(PollingCentersTableSeeder::class);
        $this->call(PollingStationsTableSeeder::class);
        // $this->call(PollingStreamsTableSeeder::class);
        $this->call(ConsulatesTableSeeder::class);
        $this->call(RefugeeCentersTableSeeder::class);
        // $this->call(BankTypesTableSeeder::class);
        // $this->call(BankCategoriesTableSeeder::class);
        // $this->call(BanksTableSeeder::class);
        $this->call(EthnicityTableSeeder::class);
        $this->call(EthnicityCategoryTableSeeder::class);
        $this->call(EthnicityTableSeeder::class);
        $this->call(ReligionTypeTableSeeder::class);
        $this->call(ReligionCategoryTableSeeder::class);
        $this->call(ReligionTableSeeder::class);
        $this->call(LanguageTypeTableSeeder::class);
        $this->call(LanguageCategoryTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(DepartmentTypesTableSeeder::class);
        $this->call(DepartmentCategoriesTableSeeder::class);
        $this->call(ServiceTypesTableSeeder::class);
        $this->call(ServiceCategoriesTableSeeder::class);
        $this->call(ActivityTypesTableSeeder::class);
        $this->call(ActivityCategoriesTableSeeder::class);
        // $this->call(ActivityNotificationsTableSeeder::class);
        $this->call(CurrencyTypesTableSeeder::class);
        $this->call(CurrencyCategoriesTableSeeder::class);
        $this->call(AccountTypesTableSeeder::class);
        $this->call(AccountCategoriesTableSeeder::class);
        $this->call(ReceiptTypesTableSeeder::class);
        $this->call(ReceiptCategoriesTableSeeder::class);
        $this->call(InvoiceTypesTableSeeder::class);
        $this->call(InvoiceCategoriesTableSeeder::class);
        $this->call(TransactionTypesTableSeeder::class);
        $this->call(TransactionCategoriesTableSeeder::class);
        $this->call(FeedbackTypesTableSeeder::class);
        $this->call(FeedbackCategoriesTableSeeder::class);
        $this->call(TicketTypesTableSeeder::class);
        $this->call(TicketCategoriesTableSeeder::class);
        $this->call(CommunicationTypesTableSeeder::class);
        $this->call(CommunicationCategoriesTableSeeder::class);
        $this->call(DepartmentsTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
        $this->call(DepartmentServiceTableSeeder::class);

        // create default administrator
        User::factory()->create([
            'name' => 'administrator',
            'email' => 'administrator@e-government.co.ke',
        ])->assignRole('administrator');

        // create default manager
        User::factory()->create([
            'name' => 'manager',
            'email' => 'manager@e-government.co.ke',
        ])->assignRole('manager');

        // create default citizen
        User::factory()->create([
            'name' => 'citizen',
            'email' => 'citizen@e-government.co.ke',
        ])->assignRole('citizen');

        // create default resident
        User::factory()->create([
            'name' => 'resident',
            'email' => 'resident@e-government.co.ke',
        ])->assignRole('resident');

        // create default refugee
        User::factory()->create([
            'name' => 'refugee',
            'email' => 'refugee@e-government.co.ke',
        ])->assignRole('refugee');

        // create default diplomat
        User::factory()->create([
            'name' => 'diplomat',
            'email' => 'diplomat@e-government.co.ke',
        ])->assignRole('diplomat');

        // create default foreigner
        User::factory()->create([
            'name' => 'foreigner',
            'email' => 'foreigner@e-government.co.ke',
        ])->assignRole('foreigner');

        // create default guest
        User::factory()->create([
            'name' => 'guest',
            'email' => 'guest@e-government.co.ke',
        ])->assignRole('guest');
    }
}
