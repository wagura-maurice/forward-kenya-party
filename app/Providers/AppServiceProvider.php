<?php

namespace App\Providers;

use App\Models\Ability;
use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountType;
use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\ActivityNotification;
use App\Models\ActivityType;
use App\Models\Administrator;
use App\Models\Bank;
use App\Models\BankCategory;
use App\Models\BankType;
use App\Models\Communication;
use App\Models\CommunicationCategory;
use App\Models\CommunicationType;
use App\Models\Constituency;
use App\Models\Country;
use App\Models\County;
use App\Models\Currency;
use App\Models\CurrencyCategory;
use App\Models\CurrencyType;
use App\Models\Department;
use App\Models\DepartmentCategory;
use App\Models\DepartmentType;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\Ethnicity;
use App\Models\EthnicityCategory;
use App\Models\EthnicityType;
use App\Models\ExchangeRate;
use App\Models\Feedback;
use App\Models\FeedbackCategory;
use App\Models\FeedbackType;
use App\Models\Guest;
use App\Models\InboundEmailMessage;
use App\Models\InboundTextMessage;
use App\Models\InboundVoiceMessage;
use App\Models\Invoice;
use App\Models\InvoiceCategory;
use App\Models\InvoiceType;
use App\Models\Journal;
use App\Models\Language;
use App\Models\LanguageCategory;
use App\Models\LanguageType;
use App\Models\Ledger;
use App\Models\Manager;
use App\Models\Media;
use App\Models\MediaCategory;
use App\Models\MediaType;
use App\Models\Member;
use App\Models\OutboundBulkEmailMessage;
use App\Models\OutboundBulkTextMessage;
use App\Models\OutboundBulkVoiceMessage;
use App\Models\OutboundEmailMessage;
use App\Models\OutboundTextMessage;
use App\Models\OutboundVoiceMessage;
use App\Models\PollingCenter;
use App\Models\PollingCenterCategory;
use App\Models\PollingCenterType;
use App\Models\PollingStation;
use App\Models\PollingStationCategory;
use App\Models\PollingStationType;
use App\Models\PollingStream;
use App\Models\PollingStreamCategory;
use App\Models\PollingStreamType;
use App\Models\Profile;
use App\Models\Receipt;
use App\Models\ReceiptCategory;
use App\Models\ReceiptType;
use App\Models\Religion;
use App\Models\ReligionCategory;
use App\Models\ReligionType;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceType;
use App\Models\Setting;
use App\Models\SubCounty;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketDialog;
use App\Models\TicketType;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionType;
use App\Models\UnstructuredSupplementaryServiceData;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Ward;
use App\Observers\AbilityObserver;
use App\Observers\AccountCategoryObserver;
use App\Observers\AccountObserver;
use App\Observers\AccountTypeObserver;
use App\Observers\ActivityCategoryObserver;
use App\Observers\ActivityNotificationObserver;
use App\Observers\ActivityObserver;
use App\Observers\ActivityTypeObserver;
use App\Observers\AdministratorObserver;
use App\Observers\BankCategoryObserver;
use App\Observers\BankObserver;
use App\Observers\BankTypeObserver;
use App\Observers\CommunicationCategoryObserver;
use App\Observers\CommunicationObserver;
use App\Observers\CommunicationTypeObserver;
use App\Observers\ConstituencyObserver;
use App\Observers\CountryObserver;
use App\Observers\CountyObserver;
use App\Observers\CurrencyCategoryObserver;
use App\Observers\CurrencyObserver;
use App\Observers\CurrencyTypeObserver;
use App\Observers\DepartmentCategoryObserver;
use App\Observers\DepartmentObserver;
use App\Observers\DepartmentTypeObserver;
use App\Observers\DocumentCategoryObserver;
use App\Observers\DocumentObserver;
use App\Observers\DocumentTypeObserver;
use App\Observers\EthnicityCategoryObserver;
use App\Observers\EthnicityObserver;
use App\Observers\EthnicityTypeObserver;
use App\Observers\ExchangeRateObserver;
use App\Observers\FeedbackCategoryObserver;
use App\Observers\FeedbackObserver;
use App\Observers\FeedbackTypeObserver;
use App\Observers\GuestObserver;
use App\Observers\InboundEmailMessageObserver;
use App\Observers\InboundTextMessageObserver;
use App\Observers\InboundVoiceMessageObserver;
use App\Observers\InvoiceCategoryObserver;
use App\Observers\InvoiceObserver;
use App\Observers\InvoiceTypeObserver;
use App\Observers\JournalObserver;
use App\Observers\LanguageCategoryObserver;
use App\Observers\LanguageObserver;
use App\Observers\LanguageTypeObserver;
use App\Observers\LedgerObserver;
use App\Observers\ManagerObserver;
use App\Observers\MediaCategoryObserver;
use App\Observers\MediaObserver;
use App\Observers\MediaTypeObserver;
use App\Observers\MemberObserver;
use App\Observers\OutboundBulkEmailMessageObserver;
use App\Observers\OutboundBulkTextMessageObserver;
use App\Observers\OutboundBulkVoiceMessageObserver;
use App\Observers\OutboundEmailMessageObserver;
use App\Observers\OutboundTextMessageObserver;
use App\Observers\OutboundVoiceMessageObserver;
use App\Observers\PollingCenterCategoryObserver;
use App\Observers\PollingCenterObserver;
use App\Observers\PollingCenterTypeObserver;
use App\Observers\PollingStationCategoryObserver;
use App\Observers\PollingStationObserver;
use App\Observers\PollingStationTypeObserver;
use App\Observers\PollingStreamCategoryObserver;
use App\Observers\PollingStreamObserver;
use App\Observers\PollingStreamTypeObserver;
use App\Observers\ProfileObserver;
use App\Observers\ReceiptCategoryObserver;
use App\Observers\ReceiptObserver;
use App\Observers\ReceiptTypeObserver;
use App\Observers\ReligionCategoryObserver;
use App\Observers\ReligionObserver;
use App\Observers\ReligionTypeObserver;
use App\Observers\RoleObserver;
use App\Observers\ServiceCategoryObserver;
use App\Observers\ServiceObserver;
use App\Observers\ServiceTypeObserver;
use App\Observers\SettingObserver;
use App\Observers\SubCountyObserver;
use App\Observers\TicketCategoryObserver;
use App\Observers\TicketDialogObserver;
use App\Observers\TicketObserver;
use App\Observers\TicketTypeObserver;
use App\Observers\TransactionCategoryObserver;
use App\Observers\TransactionObserver;
use App\Observers\TransactionTypeObserver;
use App\Observers\UnstructuredSupplementaryServiceDataObserver;
use App\Observers\UserObserver;
use App\Observers\WalletObserver;
use App\Observers\WardObserver;
use App\Policies\AbilityPolicy;
use App\Policies\AccountCategoryPolicy;
use App\Policies\AccountPolicy;
use App\Policies\AccountTypePolicy;
use App\Policies\ActivityCategoryPolicy;
use App\Policies\ActivityNotificationPolicy;
use App\Policies\ActivityPolicy;
use App\Policies\ActivityTypePolicy;
use App\Policies\AdministratorPolicy;
use App\Policies\BankCategoryPolicy;
use App\Policies\BankPolicy;
use App\Policies\BankTypePolicy;
use App\Policies\CommunicationCategoryPolicy;
use App\Policies\CommunicationPolicy;
use App\Policies\CommunicationTypePolicy;
use App\Policies\ConstituencyPolicy;
use App\Policies\CountryPolicy;
use App\Policies\CountyPolicy;
use App\Policies\CurrencyCategoryPolicy;
use App\Policies\CurrencyPolicy;
use App\Policies\CurrencyTypePolicy;
use App\Policies\DepartmentCategoryPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DepartmentTypePolicy;
use App\Policies\DocumentCategoryPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\DocumentTypePolicy;
use App\Policies\EthnicityCategoryPolicy;
use App\Policies\EthnicityPolicy;
use App\Policies\EthnicityTypePolicy;
use App\Policies\ExchangeRatePolicy;
use App\Policies\FeedbackCategoryPolicy;
use App\Policies\FeedbackPolicy;
use App\Policies\FeedbackTypePolicy;
use App\Policies\GuestPolicy;
use App\Policies\InboundEmailMessagePolicy;
use App\Policies\InboundTextMessagePolicy;
use App\Policies\InboundVoiceMessagePolicy;
use App\Policies\InvoiceCategoryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\InvoiceTypePolicy;
use App\Policies\JournalPolicy;
use App\Policies\LanguageCategoryPolicy;
use App\Policies\LanguagePolicy;
use App\Policies\LanguageTypePolicy;
use App\Policies\LedgerPolicy;
use App\Policies\ManagerPolicy;
use App\Policies\MediaCategoryPolicy;
use App\Policies\MediaPolicy;
use App\Policies\MediaTypePolicy;
use App\Policies\MemberPolicy;
use App\Policies\OutboundBulkEmailMessagePolicy;
use App\Policies\OutboundBulkTextMessagePolicy;
use App\Policies\OutboundBulkVoiceMessagePolicy;
use App\Policies\OutboundEmailMessagePolicy;
use App\Policies\OutboundTextMessagePolicy;
use App\Policies\OutboundVoiceMessagePolicy;
use App\Policies\PollingCenterCategoryPolicy;
use App\Policies\PollingCenterPolicy;
use App\Policies\PollingCenterTypePolicy;
use App\Policies\PollingStationCategoryPolicy;
use App\Policies\PollingStationPolicy;
use App\Policies\PollingStationTypePolicy;
use App\Policies\PollingStreamCategoryPolicy;
use App\Policies\PollingStreamPolicy;
use App\Policies\PollingStreamTypePolicy;
use App\Policies\ProfilePolicy;
use App\Policies\ReceiptCategoryPolicy;
use App\Policies\ReceiptPolicy;
use App\Policies\ReceiptTypePolicy;
use App\Policies\ReligionCategoryPolicy;
use App\Policies\ReligionPolicy;
use App\Policies\ReligionTypePolicy;
use App\Policies\RolePolicy;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\ServicePolicy;
use App\Policies\ServiceTypePolicy;
use App\Policies\SettingPolicy;
use App\Policies\SubCountyPolicy;
use App\Policies\TicketCategoryPolicy;
use App\Policies\TicketDialogPolicy;
use App\Policies\TicketPolicy;
use App\Policies\TicketTypePolicy;
use App\Policies\TransactionCategoryPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\TransactionTypePolicy;
use App\Policies\UnstructuredSupplementaryServiceDataPolicy;
use App\Policies\UserPolicy;
use App\Policies\WalletPolicy;
use App\Policies\WardPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

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
            // DB::statement('SET SESSION sql_require_primary_key=0');
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
        
        // telephone validation for kenya only
        Validator::extend('telephone', function ($attribute, $value, $parameters, $validator) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            
            try {
                // Parse the phone number and ensure the region is valid
                $phoneNumber = $phoneUtil->parse($value, 'KE');
        
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
        Member::observe(MemberObserver::class);
        Communication::observe(CommunicationObserver::class);
        CommunicationCategory::observe(CommunicationCategoryObserver::class);
        CommunicationType::observe(CommunicationTypeObserver::class);
        Constituency::observe(ConstituencyObserver::class);
        County::observe(CountyObserver::class);
        Currency::observe(CurrencyObserver::class);
        CurrencyCategory::observe(CurrencyCategoryObserver::class);
        CurrencyType::observe(CurrencyTypeObserver::class);
        Department::observe(DepartmentObserver::class);
        DepartmentCategory::observe(DepartmentCategoryObserver::class);
        DepartmentType::observe(DepartmentTypeObserver::class);
        Document::observe(DocumentObserver::class);
        DocumentCategory::observe(DocumentCategoryObserver::class);
        DocumentType::observe(DocumentTypeObserver::class);
        ExchangeRate::observe(ExchangeRateObserver::class);
        Feedback::observe(FeedbackObserver::class);
        FeedbackCategory::observe(FeedbackCategoryObserver::class);
        FeedbackType::observe(FeedbackTypeObserver::class);
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
        // Notification observers have been removed as part of notification system update
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
        
        // Ethnicity observers
        Ethnicity::observe(EthnicityObserver::class);
        EthnicityCategory::observe(EthnicityCategoryObserver::class);
        EthnicityType::observe(EthnicityTypeObserver::class);
        
        // Religion observers
        Religion::observe(ReligionObserver::class);
        ReligionCategory::observe(ReligionCategoryObserver::class);
        ReligionType::observe(ReligionTypeObserver::class);
        
        // Language observers
        Language::observe(LanguageObserver::class);
        LanguageCategory::observe(LanguageCategoryObserver::class);
        LanguageType::observe(LanguageTypeObserver::class);
        
        // Polling observers
        PollingCenter::observe(PollingCenterObserver::class);
        PollingCenterCategory::observe(PollingCenterCategoryObserver::class);
        PollingCenterType::observe(PollingCenterTypeObserver::class);
        PollingStation::observe(PollingStationObserver::class);
        PollingStationCategory::observe(PollingStationCategoryObserver::class);
        PollingStationType::observe(PollingStationTypeObserver::class);
        PollingStream::observe(PollingStreamObserver::class);
        PollingStreamCategory::observe(PollingStreamCategoryObserver::class);
        PollingStreamType::observe(PollingStreamTypeObserver::class);
        
        // Additional observers
        TicketDialog::observe(TicketDialogObserver::class);
        UnstructuredSupplementaryServiceData::observe(UnstructuredSupplementaryServiceDataObserver::class);
        
        // App Policies.
        Gate::policy(Ability::class, AbilityPolicy::class);
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(AccountCategory::class, AccountCategoryPolicy::class);
        Gate::policy(ActivityNotification::class, ActivityNotificationPolicy::class);
        Gate::policy(AccountType::class, AccountTypePolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(ActivityCategory::class, ActivityCategoryPolicy::class);
        Gate::policy(ActivityType::class, ActivityTypePolicy::class);
        Gate::policy(Administrator::class, AdministratorPolicy::class);
        Gate::policy(Bank::class, BankPolicy::class);
        Gate::policy(BankCategory::class, BankCategoryPolicy::class);
        Gate::policy(BankType::class, BankTypePolicy::class);
        Gate::policy(Member::class, MemberPolicy::class);
        Gate::policy(Communication::class, CommunicationPolicy::class);
        Gate::policy(CommunicationCategory::class, CommunicationCategoryPolicy::class);
        Gate::policy(CommunicationType::class, CommunicationTypePolicy::class);
        Gate::policy(Constituency::class, ConstituencyPolicy::class);
        Gate::policy(County::class, CountyPolicy::class);
        Gate::policy(Currency::class, CurrencyPolicy::class);
        Gate::policy(CurrencyCategory::class, CurrencyCategoryPolicy::class);
        Gate::policy(CurrencyType::class, CurrencyTypePolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(DepartmentCategory::class, DepartmentCategoryPolicy::class);
        Gate::policy(DepartmentType::class, DepartmentTypePolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(DocumentCategory::class, DocumentCategoryPolicy::class);
        Gate::policy(DocumentType::class, DocumentTypePolicy::class);
        Gate::policy(ExchangeRate::class, ExchangeRatePolicy::class);
        Gate::policy(Feedback::class, FeedbackPolicy::class);
        Gate::policy(FeedbackCategory::class, FeedbackCategoryPolicy::class);
        Gate::policy(FeedbackType::class, FeedbackTypePolicy::class);
        Gate::policy(Guest::class, GuestPolicy::class);
        Gate::policy(InboundEmailMessage::class, InboundEmailMessagePolicy::class);
        Gate::policy(InboundTextMessage::class, InboundTextMessagePolicy::class);
        Gate::policy(InboundVoiceMessage::class, InboundVoiceMessagePolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(InvoiceCategory::class, InvoiceCategoryPolicy::class);
        Gate::policy(InvoiceType::class, InvoiceTypePolicy::class);
        Gate::policy(Journal::class, JournalPolicy::class);
        Gate::policy(Ledger::class, LedgerPolicy::class);
        Gate::policy(Manager::class, ManagerPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(MediaCategory::class, MediaCategoryPolicy::class);
        Gate::policy(MediaType::class, MediaTypePolicy::class);
        Gate::policy(OutboundBulkEmailMessage::class, OutboundBulkEmailMessagePolicy::class);
        Gate::policy(OutboundBulkTextMessage::class, OutboundBulkTextMessagePolicy::class);
        Gate::policy(OutboundBulkVoiceMessage::class, OutboundBulkVoiceMessagePolicy::class);
        Gate::policy(OutboundEmailMessage::class, OutboundEmailMessagePolicy::class);
        Gate::policy(OutboundTextMessage::class, OutboundTextMessagePolicy::class);
        Gate::policy(OutboundVoiceMessage::class, OutboundVoiceMessagePolicy::class);
        Gate::policy(Profile::class, ProfilePolicy::class);
        Gate::policy(Receipt::class, ReceiptPolicy::class);
        Gate::policy(ReceiptCategory::class, ReceiptCategoryPolicy::class);
        Gate::policy(ReceiptType::class, ReceiptTypePolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(ServiceCategory::class, ServiceCategoryPolicy::class);
        Gate::policy(ServiceType::class, ServiceTypePolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(SubCounty::class, SubCountyPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(TicketCategory::class, TicketCategoryPolicy::class);
        Gate::policy(TicketType::class, TicketTypePolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(TransactionCategory::class, TransactionCategoryPolicy::class);
        Gate::policy(TransactionType::class, TransactionTypePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Wallet::class, WalletPolicy::class);
        Gate::policy(Ward::class, WardPolicy::class);
        
        // Ethnicity policies
        Gate::policy(Ethnicity::class, EthnicityPolicy::class);
        Gate::policy(EthnicityCategory::class, EthnicityCategoryPolicy::class);
        Gate::policy(EthnicityType::class, EthnicityTypePolicy::class);
        
        // Religion policies
        Gate::policy(Religion::class, ReligionPolicy::class);
        Gate::policy(ReligionCategory::class, ReligionCategoryPolicy::class);
        Gate::policy(ReligionType::class, ReligionTypePolicy::class);
        
        // Language policies
        Gate::policy(Language::class, LanguagePolicy::class);
        Gate::policy(LanguageCategory::class, LanguageCategoryPolicy::class);
        Gate::policy(LanguageType::class, LanguageTypePolicy::class);
        
        // Polling policies
        Gate::policy(PollingCenter::class, PollingCenterPolicy::class);
        Gate::policy(PollingCenterCategory::class, PollingCenterCategoryPolicy::class);
        Gate::policy(PollingCenterType::class, PollingCenterTypePolicy::class);
        Gate::policy(PollingStation::class, PollingStationPolicy::class);
        Gate::policy(PollingStationCategory::class, PollingStationCategoryPolicy::class);
        Gate::policy(PollingStationType::class, PollingStationTypePolicy::class);
        Gate::policy(PollingStream::class, PollingStreamPolicy::class);
        Gate::policy(PollingStreamCategory::class, PollingStreamCategoryPolicy::class);
        Gate::policy(PollingStreamType::class, PollingStreamTypePolicy::class);
        
        // Additional policies
        Gate::policy(TicketDialog::class, TicketDialogPolicy::class);
        Gate::policy(UnstructuredSupplementaryServiceData::class, UnstructuredSupplementaryServiceDataPolicy::class);
    }
}
