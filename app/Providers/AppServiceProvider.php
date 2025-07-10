<?php

namespace App\Providers;

use App\Models\Bank;
use App\Models\Role;
use App\Models\User;
use App\Models\Ward;
use App\Models\Guest;
use App\Models\Media;
use App\Models\County;
use App\Models\Ledger;
use App\Models\Ticket;
use App\Models\Wallet;
use App\Models\Ability;
use App\Models\Account;
use App\Models\Citizen;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Manager;
use App\Models\Profile;
use App\Models\Receipt;
use App\Models\Refugee;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Activity;
use App\Models\BankType;
use App\Models\Currency;
use App\Models\Diplomat;
use App\Models\Document;
use App\Models\Feedback;
use App\Models\Resident;
use App\Models\Consulate;
use App\Models\Foreigner;
use App\Models\MediaType;
use App\Models\SubCounty;
use App\Models\Department;
use App\Models\TicketType;
use App\Models\AccountType;
use App\Models\InvoiceType;
use App\Models\ReceiptType;
use App\Models\ServiceType;
use App\Models\Transaction;
use App\Models\ActivityType;
use App\Models\BankCategory;
use App\Models\Constituency;
use App\Models\CurrencyType;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\FeedbackType;
use App\Policies\BankPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\WardPolicy;
use App\Models\Administrator;
use App\Models\Communication;
use App\Models\MediaCategory;
use App\Models\RefugeeCenter;
use App\Policies\GuestPolicy;
use App\Policies\MediaPolicy;
use App\Models\DepartmentType;
use App\Models\TicketCategory;
use App\Policies\CountyPolicy;
use App\Policies\LedgerPolicy;
use App\Policies\TicketPolicy;
use App\Policies\WalletPolicy;
use App\Models\AccountCategory;
use App\Models\InvoiceCategory;
use App\Models\ReceiptCategory;
use App\Models\ServiceCategory;
use App\Models\TransactionType;
use App\Observers\BankObserver;
use App\Observers\RoleObserver;
use App\Observers\UserObserver;
use App\Observers\WardObserver;
use App\Policies\AbilityPolicy;
use App\Policies\AccountPolicy;
use App\Policies\CitizenPolicy;
use App\Policies\CountryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\JournalPolicy;
use App\Policies\ManagerPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\ReceiptPolicy;
use App\Policies\RefugeePolicy;
use App\Policies\ServicePolicy;
use App\Policies\SettingPolicy;
use App\Models\ActivityCategory;
use App\Models\CurrencyCategory;
use App\Models\DocumentCategory;
use App\Models\FeedbackCategory;
use App\Observers\GuestObserver;
use App\Observers\MediaObserver;
use App\Policies\ActivityPolicy;
use App\Policies\BankTypePolicy;
use App\Policies\CurrencyPolicy;
use App\Policies\DiplomatPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\FeedbackPolicy;
use App\Policies\ResidentPolicy;
use App\Models\CommunicationType;
use App\Observers\CountyObserver;
use App\Observers\LedgerObserver;
use App\Observers\TicketObserver;
use App\Observers\WalletObserver;
use App\Policies\ConsulatePolicy;
use App\Policies\ForeignerPolicy;
use App\Policies\MediaTypePolicy;
use App\Policies\SubCountyPolicy;
use App\Models\DepartmentCategory;
use App\Models\InboundTextMessage;
use App\Observers\AbilityObserver;
use App\Observers\AccountObserver;
use App\Observers\CitizenObserver;
use App\Observers\CountryObserver;
use App\Observers\InvoiceObserver;
use App\Observers\JournalObserver;
use App\Observers\ManagerObserver;
use App\Observers\ProfileObserver;
use App\Observers\ReceiptObserver;
use App\Observers\RefugeeObserver;
use App\Observers\ServiceObserver;
use App\Observers\SettingObserver;
use App\Policies\DepartmentPolicy;
use App\Policies\TicketTypePolicy;
use Illuminate\Support\Facades\DB;
use App\Models\InboundEmailMessage;
use App\Models\InboundVoiceMessage;
use App\Models\OutboundTextMessage;
use App\Models\TransactionCategory;
use App\Observers\ActivityObserver;
use App\Observers\BankTypeObserver;
use App\Observers\CurrencyObserver;
use App\Observers\DiplomatObserver;
use App\Observers\DocumentObserver;
use App\Observers\FeedbackObserver;
use App\Observers\ResidentObserver;
use App\Policies\AccountTypePolicy;
use App\Policies\InvoiceTypePolicy;
use App\Policies\ReceiptTypePolicy;
use App\Policies\ServiceTypePolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\URL;
use libphonenumber\PhoneNumberUtil;
use App\Models\ActivityNotification;
use App\Models\OutboundEmailMessage;
use App\Models\OutboundVoiceMessage;
use App\Observers\ConsulateObserver;
use App\Observers\ForeignerObserver;
use App\Observers\MediaTypeObserver;
use App\Observers\SubCountyObserver;
use App\Policies\ActivityTypePolicy;
use App\Policies\BankCategoryPolicy;
use App\Policies\ConstituencyPolicy;
use App\Policies\CurrencyTypePolicy;
use App\Policies\DocumentTypePolicy;
use App\Policies\ExchangeRatePolicy;
use App\Policies\FeedbackTypePolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\CommunicationCategory;
use App\Observers\DepartmentObserver;
use App\Observers\TicketTypeObserver;
use App\Policies\AdministratorPolicy;
use App\Policies\CommunicationPolicy;
use App\Policies\MediaCategoryPolicy;
use App\Policies\RefugeeCenterPolicy;
use App\Observers\AccountTypeObserver;
use App\Observers\InvoiceTypeObserver;
use App\Observers\ReceiptTypeObserver;
use App\Observers\ServiceTypeObserver;
use App\Observers\TransactionObserver;
use App\Policies\DepartmentTypePolicy;
use App\Policies\TicketCategoryPolicy;
use Illuminate\Support\Facades\Schema;
use App\Models\OutboundBulkTextMessage;
use App\Observers\ActivityTypeObserver;
use App\Observers\BankCategoryObserver;
use App\Observers\ConstituencyObserver;
use App\Observers\CurrencyTypeObserver;
use App\Observers\DocumentTypeObserver;
use App\Observers\ExchangeRateObserver;
use App\Observers\FeedbackTypeObserver;
use App\Policies\AccountCategoryPolicy;
use App\Policies\InvoiceCategoryPolicy;
use App\Policies\ReceiptCategoryPolicy;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\TransactionTypePolicy;
use Illuminate\Support\ServiceProvider;
use App\Models\OutboundBulkEmailMessage;
use App\Models\OutboundBulkVoiceMessage;
use App\Observers\AdministratorObserver;
use App\Observers\CommunicationObserver;
use App\Observers\MediaCategoryObserver;
use App\Observers\RefugeeCenterObserver;
use App\Policies\ActivityCategoryPolicy;
use App\Policies\CurrencyCategoryPolicy;
use App\Policies\DocumentCategoryPolicy;
use App\Policies\FeedbackCategoryPolicy;
use libphonenumber\NumberParseException;
use App\Observers\DepartmentTypeObserver;
use App\Observers\TicketCategoryObserver;
use App\Policies\CommunicationTypePolicy;
use Illuminate\Support\Facades\Validator;
use App\Observers\AccountCategoryObserver;
use App\Observers\InvoiceCategoryObserver;
use App\Observers\ReceiptCategoryObserver;
use App\Observers\ServiceCategoryObserver;
use App\Observers\TransactionTypeObserver;
use App\Policies\DepartmentCategoryPolicy;
use App\Policies\InboundTextMessagePolicy;
use App\Observers\ActivityCategoryObserver;
use App\Observers\CurrencyCategoryObserver;
use App\Observers\DocumentCategoryObserver;
use App\Observers\FeedbackCategoryObserver;
use App\Policies\InboundEmailMessagePolicy;
use App\Policies\InboundVoiceMessagePolicy;
use App\Policies\OutboundTextMessagePolicy;
use App\Policies\TransactionCategoryPolicy;
use App\Observers\CommunicationTypeObserver;
use App\Policies\ActivityNotificationPolicy;
use App\Policies\OutboundEmailMessagePolicy;
use App\Policies\OutboundVoiceMessagePolicy;
use App\Observers\DepartmentCategoryObserver;
use App\Observers\InboundTextMessageObserver;
use App\Policies\CommunicationCategoryPolicy;
use App\Observers\InboundEmailMessageObserver;
use App\Observers\InboundVoiceMessageObserver;
use App\Observers\OutboundTextMessageObserver;
use App\Observers\TransactionCategoryObserver;
use App\Observers\ActivityNotificationObserver;
use App\Observers\OutboundEmailMessageObserver;
use App\Observers\OutboundVoiceMessageObserver;
use App\Policies\OutboundBulkTextMessagePolicy;
use App\Observers\CommunicationCategoryObserver;
use App\Policies\OutboundBulkEmailMessagePolicy;
use App\Policies\OutboundBulkVoiceMessagePolicy;
use App\Observers\OutboundBulkTextMessageObserver;
use App\Observers\OutboundBulkEmailMessageObserver;
use App\Observers\OutboundBulkVoiceMessageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // App Environment.
        if (env('APP_ENV') === 'production') {
            // primary requirement for digital ocean MySQL network
            DB::statement('SET SESSION sql_require_primary_key=0');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {        
        // App Environment.
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // Define a global timestamp for the request cycle
        if (!defined('REQUEST_TIMESTAMP')) {
            define('REQUEST_TIMESTAMP', now());
        }
        
        // App Db Schema.
        Schema::defaultStringLength(191);
        
        // telephone validation for the whole world
        Validator::extend('telephone', function ($attribute, $value, $parameters, $validator) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            
            try {
                // Parse the phone number and ensure the region is valid
                $phoneNumber = $phoneUtil->parse($value);
        
                // Check if the phone number is valid
                return $phoneUtil->isValidNumber($phoneNumber);
            } catch (NumberParseException $e) {
                return false;
            }
        });
        
        // App Observers.
        Ability::observe(AbilityObserver::class);
        Account::observe(AccountObserver::class);
        AccountCategory::observe(AccountCategoryObserver::class);
        ActivityNotification::observe(ActivityNotificationObserver::class);
        AccountType::observe(AccountTypeObserver::class);
        Activity::observe(ActivityObserver::class);
        ActivityCategory::observe(ActivityCategoryObserver::class);
        ActivityType::observe(ActivityTypeObserver::class);
        Administrator::observe(AdministratorObserver::class);
        Bank::observe(BankObserver::class);
        BankCategory::observe(BankCategoryObserver::class);
        BankType::observe(BankTypeObserver::class);
        Citizen::observe(CitizenObserver::class);
        Communication::observe(CommunicationObserver::class);
        CommunicationCategory::observe(CommunicationCategoryObserver::class);
        CommunicationType::observe(CommunicationTypeObserver::class);
        Constituency::observe(ConstituencyObserver::class);
        Consulate::observe(ConsulateObserver::class);
        Country::observe(CountryObserver::class);
        County::observe(CountyObserver::class);
        Currency::observe(CurrencyObserver::class);
        CurrencyCategory::observe(CurrencyCategoryObserver::class);
        CurrencyType::observe(CurrencyTypeObserver::class);
        Department::observe(DepartmentObserver::class);
        DepartmentCategory::observe(DepartmentCategoryObserver::class);
        DepartmentType::observe(DepartmentTypeObserver::class);
        Diplomat::observe(DiplomatObserver::class);
        Document::observe(DocumentObserver::class);
        DocumentCategory::observe(DocumentCategoryObserver::class);
        DocumentType::observe(DocumentTypeObserver::class);
        ExchangeRate::observe(ExchangeRateObserver::class);
        Feedback::observe(FeedbackObserver::class);
        FeedbackCategory::observe(FeedbackCategoryObserver::class);
        FeedbackType::observe(FeedbackTypeObserver::class);
        Foreigner::observe(ForeignerObserver::class);
        Guest::observe(GuestObserver::class);
        InboundEmailMessage::observe(InboundEmailMessageObserver::class);
        InboundTextMessage::observe(InboundTextMessageObserver::class);
        InboundVoiceMessage::observe(InboundVoiceMessageObserver::class);
        Invoice::observe(InvoiceObserver::class);
        InvoiceCategory::observe(InvoiceCategoryObserver::class);
        InvoiceType::observe(InvoiceTypeObserver::class);
        Journal::observe(JournalObserver::class);
        Ledger::observe(LedgerObserver::class);
        Manager::observe(ManagerObserver::class);
        Media::observe(MediaObserver::class);
        MediaCategory::observe(MediaCategoryObserver::class);
        MediaType::observe(MediaTypeObserver::class);
        // Notification observers have been removed as part of the notification system update
        OutboundBulkEmailMessage::observe(OutboundBulkEmailMessageObserver::class);
        OutboundBulkTextMessage::observe(OutboundBulkTextMessageObserver::class);
        OutboundBulkVoiceMessage::observe(OutboundBulkVoiceMessageObserver::class);
        OutboundEmailMessage::observe(OutboundEmailMessageObserver::class);
        OutboundTextMessage::observe(OutboundTextMessageObserver::class);
        OutboundVoiceMessage::observe(OutboundVoiceMessageObserver::class);
        Profile::observe(ProfileObserver::class);
        Receipt::observe(ReceiptObserver::class);
        ReceiptCategory::observe(ReceiptCategoryObserver::class);
        ReceiptType::observe(ReceiptTypeObserver::class);
        Refugee::observe(RefugeeObserver::class);
        RefugeeCenter::observe(RefugeeCenterObserver::class);
        Resident::observe(ResidentObserver::class);
        Role::observe(RoleObserver::class);
        Service::observe(ServiceObserver::class);
        ServiceCategory::observe(ServiceCategoryObserver::class);
        ServiceType::observe(ServiceTypeObserver::class);
        Setting::observe(SettingObserver::class);
        SubCounty::observe(SubCountyObserver::class);
        Ticket::observe(TicketObserver::class);
        TicketCategory::observe(TicketCategoryObserver::class);
        TicketType::observe(TicketTypeObserver::class);
        Transaction::observe(TransactionObserver::class);
        TransactionCategory::observe(TransactionCategoryObserver::class);
        TransactionType::observe(TransactionTypeObserver::class);
        User::observe(UserObserver::class);
        Wallet::observe(WalletObserver::class);
        Ward::observe(WardObserver::class);
        
        // App Policies.
        Gate::policy(Ability::class, AbilityPolicy::class);
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(AccountCategory::class, AccountCategoryPolicy::class);
        Gate::policy(ActivityNotification::class, ActivityNotificationPolicy::class);
        Gate::define('view_account_types', [AccountTypePolicy::class, 'view']);
        Gate::define('view_activities', [ActivityPolicy::class, 'view']);
        Gate::define('view_activity_categories', [ActivityCategoryPolicy::class, 'view']);
        Gate::define('view_activity_types', [ActivityTypePolicy::class, 'view']);
        Gate::define('view_administrators', [AdministratorPolicy::class, 'view']);
        Gate::define('view_banks', [BankPolicy::class, 'view']);
        Gate::define('view_bank_categories', [BankCategoryPolicy::class, 'view']);
        Gate::define('view_bank_types', [BankTypePolicy::class, 'view']);
        Gate::define('view_citizens', [CitizenPolicy::class, 'view']);
        Gate::define('view_communications', [CommunicationPolicy::class, 'view']);
        Gate::define('view_communication_categories', [CommunicationCategoryPolicy::class, 'view']);
        Gate::define('view_communication_types', [CommunicationTypePolicy::class, 'view']);
        Gate::define('view_constituencies', [ConstituencyPolicy::class, 'view']);
        Gate::define('view_consulates', [ConsulatePolicy::class, 'view']);
        Gate::define('view_countries', [CountryPolicy::class, 'view']);
        Gate::define('view_counties', [CountyPolicy::class, 'view']);
        Gate::define('view_currencies', [CurrencyPolicy::class, 'view']);
        Gate::define('view_currency_categories', [CurrencyCategoryPolicy::class, 'view']);
        Gate::define('view_currency_types', [CurrencyTypePolicy::class, 'view']);
        Gate::define('view_departments', [DepartmentPolicy::class, 'view']);
        Gate::define('view_department_categories', [DepartmentCategoryPolicy::class, 'view']);
        Gate::define('view_department_types', [DepartmentTypePolicy::class, 'view']);
        Gate::define('view_diplomats', [DiplomatPolicy::class, 'view']);
        Gate::define('view_documents', [DocumentPolicy::class, 'view']);
        Gate::define('view_document_categories', [DocumentCategoryPolicy::class, 'view']);
        Gate::define('view_document_types', [DocumentTypePolicy::class, 'view']);
        Gate::define('view_exchange_rates', [ExchangeRatePolicy::class, 'view']);
        Gate::define('view_feedbacks', [FeedbackPolicy::class, 'view']);
        Gate::define('view_feedback_categories', [FeedbackCategoryPolicy::class, 'view']);
        Gate::define('view_feedback_types', [FeedbackTypePolicy::class, 'view']);
        Gate::define('view_foreigners', [ForeignerPolicy::class, 'view']);
        Gate::define('view_guests', [GuestPolicy::class, 'view']);
        Gate::define('view_inbound_mail_messages', [InboundEmailMessagePolicy::class, 'view']);
        Gate::define('view_inbound_text_messages', [InboundTextMessagePolicy::class, 'view']);
        Gate::define('view_inbound_voice_messages', [InboundVoiceMessagePolicy::class, 'view']);
        Gate::define('view_invoices', [InvoicePolicy::class, 'view']);
        Gate::define('view_invoice_categories', [InvoiceCategoryPolicy::class, 'view']);
        Gate::define('view_invoice_types', [InvoiceTypePolicy::class, 'view']);
        Gate::define('view_journals', [JournalPolicy::class, 'view']);
        Gate::define('view_ledgers', [LedgerPolicy::class, 'view']);
        Gate::define('view_managers', [ManagerPolicy::class, 'view']);
        Gate::define('view_media', [MediaPolicy::class, 'view']);
        Gate::define('view_media_categories', [MediaCategoryPolicy::class, 'view']);
        Gate::define('view_media_types', [MediaTypePolicy::class, 'view']);
        // Notification related gates have been removed as part of the notification system update
        Gate::define('view_outbound_bulk_email_messages', [OutboundBulkEmailMessagePolicy::class, 'view']);
        Gate::define('view_outbound_bulk_text_messages', [OutboundBulkTextMessagePolicy::class, 'view']);
        Gate::define('view_outbound_bulk_voice_messages', [OutboundBulkVoiceMessagePolicy::class, 'view']);
        Gate::define('view_outbound_email_messages', [OutboundEmailMessagePolicy::class, 'view']);
        Gate::define('view_outbound_text_messages', [OutboundTextMessagePolicy::class, 'view']);
        Gate::define('view_outbound_voice_messages', [OutboundVoiceMessagePolicy::class, 'view']);
        Gate::define('view_profiles', [ProfilePolicy::class, 'view']);
        Gate::define('view_receipts', [ReceiptPolicy::class, 'view']);
        Gate::define('view_receipt_categories', [ReceiptCategoryPolicy::class, 'view']);
        Gate::define('view_receipt_types', [ReceiptTypePolicy::class, 'view']);
        Gate::define('view_refugees', [RefugeePolicy::class, 'view']);
        Gate::define('view_refugee_centers', [RefugeeCenterPolicy::class, 'view']);
        Gate::define('view_residents', [ResidentPolicy::class, 'view']);
        Gate::define('view_roles', [RolePolicy::class, 'view']);
        Gate::define('view_services', [ServicePolicy::class, 'view']);
        Gate::define('view_service_categories', [ServiceCategoryPolicy::class, 'view']);
        Gate::define('view_service_types', [ServiceTypePolicy::class, 'view']);
        Gate::define('view_settings', [SettingPolicy::class, 'view']);
        Gate::define('view_sub_counties', [SubCountyPolicy::class, 'view']);
        Gate::define('view_tickets', [TicketPolicy::class, 'view']);
        Gate::define('view_ticket_categories', [TicketCategoryPolicy::class, 'view']);
        Gate::define('view_ticket_types', [TicketTypePolicy::class, 'view']);
        Gate::define('view_transactions', [TransactionPolicy::class, 'view']);
        Gate::define('view_transaction_categories', [TransactionCategoryPolicy::class, 'view']);
        Gate::define('view_transaction_types', [TransactionTypePolicy::class, 'view']);
        Gate::define('view_users', [UserPolicy::class, 'view']);
        Gate::define('view_wallets', [WalletPolicy::class, 'view']);
        Gate::define('view_wards', [WardPolicy::class, 'view']);
    }
}
