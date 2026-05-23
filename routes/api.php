<?php
// routes/api.php
use Illuminate\Http\Request;
use App\Http\Controllers\WahaWebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\CurrencyController;
use App\Http\Controllers\API\AccountTypeController;
use App\Http\Controllers\API\AccountCategoryController;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\CommunicationController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\WardController;
use App\Http\Controllers\API\GuestController;
use App\Http\Controllers\API\MediaController;
use App\Http\Controllers\API\CountyController;
use App\Http\Controllers\API\LedgerController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\AbilityController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\JournalController;
use App\Http\Controllers\API\ManagerController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ReceiptController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\MediaTypeController;
use App\Http\Controllers\API\SubCountyController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\TicketTypeController;
use App\Http\Controllers\AfricaIsTalkingController;
use App\Http\Controllers\API\InvoiceTypeController;
use App\Http\Controllers\API\ReceiptTypeController;
use App\Http\Controllers\API\ServiceTypeController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\ActivityTypeController;
use App\Http\Controllers\API\ConstituencyController;
use App\Http\Controllers\API\CurrencyTypeController;
use App\Http\Controllers\API\DocumentTypeController;
use App\Http\Controllers\API\ExchangeRateController;
use App\Http\Controllers\API\FeedbackTypeController;
use App\Http\Controllers\API\AdministratorController;
use App\Http\Controllers\API\MediaCategoryController;
use App\Http\Controllers\LipaNaMpesaOnlineController;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\DepartmentTypeController;
use App\Http\Controllers\API\TicketCategoryController;
use App\Http\Controllers\API\InvoiceCategoryController;
use App\Http\Controllers\API\LightOfGuidanceController;
use App\Http\Controllers\API\ReceiptCategoryController;
use App\Http\Controllers\API\ServiceCategoryController;
use App\Http\Controllers\API\TransactionTypeController;
use App\Http\Controllers\API\ActivityCategoryController;
use App\Http\Controllers\API\CurrencyCategoryController;
use App\Http\Controllers\API\DocumentCategoryController;
use App\Http\Controllers\API\FeedbackCategoryController;
use App\Http\Controllers\API\CommunicationTypeController;
use App\Http\Controllers\API\DepartmentCategoryController;
use App\Http\Controllers\API\InboundTextMessageController;
use App\Http\Controllers\API\InboundEmailMessageController;
use App\Http\Controllers\API\InboundVoiceMessageController;
use App\Http\Controllers\API\OutboundTextMessageController;
use App\Http\Controllers\API\TransactionCategoryController;
use App\Http\Controllers\API\ActivityNotificationController;
use App\Http\Controllers\API\OutboundEmailMessageController;
use App\Http\Controllers\API\OutboundVoiceMessageController;
use App\Http\Controllers\API\CommunicationCategoryController;
use App\Http\Controllers\API\OutboundBulkTextMessageController;
use App\Http\Controllers\API\OutboundBulkEmailMessageController;
use App\Http\Controllers\API\OutboundBulkVoiceMessageController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

# (*) public access routes

// register & login authentication
Route::group(['prefix' => 'auth'], function () {
    Route::post('/sign-up', [AuthenticationController::class, 'signUp'])->name('auth.sign-up');
    Route::post('/sign-in', [AuthenticationController::class, 'signIn'])->name('auth.sign-in');
    Route::post('/forgot-password', [AuthenticationController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/reset-password', [AuthenticationController::class, 'resetPassword'])->name('auth.reset-password');
    Route::get('/request-email-verification', [AuthenticationController::class, 'requestEmailVerification'])->name('auth.request-email-verification');
    Route::post('/verify-email', [AuthenticationController::class, 'verifyEmail'])->name('auth.verify-email');
    // Old OTP routes removed - now using IPPMS for OTP verification
    // Route::post('/request-otp', [AuthenticationController::class, 'requestOTP'])->name('auth.request-otp');
    // Route::post('/verify-otp', [AuthenticationController::class, 'verifyOTP'])->name('auth.verify-otp');
    Route::post('/check-membership-status', [AuthenticationController::class, 'checkMembershipStatus'])->name('auth.check-membership-status');
    Route::post('/request-ippms-confirmation-code', [AuthenticationController::class, 'requestIppmsConfirmationCode'])->name('auth.request-ippms-confirmation-code');
    Route::post('/complete-ippms-registration', [AuthenticationController::class, 'completeIppmsRegistration'])->name('auth.complete-ippms-registration');
    Route::post('/register-without-login', [AuthenticationController::class, 'registerWithoutLogin'])->name('auth.register-without-login');
    Route::post('/login-after-ippms-verification', [AuthenticationController::class, 'loginAfterIppmsVerification'])->name('auth.login-after-ippms-verification');
    Route::middleware('auth:sanctum')->get('/current-member', [AuthenticationController::class, 'getCurrentMember'])->name('auth.current-member');
    Route::middleware('auth:sanctum')->post('/sign-out', [AuthenticationController::class, 'signOut'])->name('auth.sign-out');
});

// africa is yapping text message callback services
Route::group(['prefix' => '/ian'], function () {             
    Route::group(['prefix' => '/at'], function () {
        Route::post('/delivery-reports', [AfricaIsTalkingController::class, 'atDeliveryReports'])->name('at.delivery-reports');
        Route::post('/bulk-sms-opt-out', [AfricaIsTalkingController::class, 'atBulkSMSOptOut'])->name('at.bulk-sms-opt-out');
    });
});

// mpesa payments gateway - instant payment network.
Route::group(['prefix' => '/ipn'], function () {
    Route::group(['prefix' => '/mpesa/daraja'], function () {
        Route::post('/lnmo/transact', [LipaNaMpesaOnlineController::class, 'transact'])->name('mpesa.daraja.lnmo.transact');
        Route::post('/lnmo/query', [LipaNaMpesaOnlineController::class, 'query'])->name('mpesa.daraja.lnmo.query');
        Route::post('/lnmo/callback', [LipaNaMpesaOnlineController::class, 'callback'])->name('mpesa.daraja.lnmo.callback');
    });
});

// WAHA WhatsApp Webhook Routes (no middleware for external access)
Route::group(['prefix' => 'webhook'], function () {
    Route::post('/waha', [WahaWebhookController::class, 'handleWebhook'])->name('webhook.waha');
});

# (*) protected access routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // light of guidances management
    Route::apiResource('/light/of/guidances', LightOfGuidanceController::class, ['as' => 'light.of.guidances']);
    // settings management
    Route::apiResource('/setting', SettingController::class, ['as' => 'setting']);
    // user role management
    Route::apiResource('/role', RoleController::class, ['as' => 'role']);
    // user ability management
    Route::apiResource('/ability', AbilityController::class, ['as' => 'ability']);
    // user profile management
    Route::apiResource('/profile', ProfileController::class, ['as' => 'profile']);
    // user county management
    Route::apiResource('/county', CountyController::class, ['as' => 'county']);
    // sub_county management
    Route::apiResource('/sub/county', SubCountyController::class, ['as' => 'sub.county']);
    // constituency management
    Route::apiResource('/constituency', ConstituencyController::class, ['as' => 'constituency']);
    // ward management
    Route::apiResource('/ward', WardController::class, ['as' => 'ward']);
    // administrator management
    Route::apiResource('/administrator', AdministratorController::class, ['as' => 'administrator']);
    // manager management
    Route::apiResource('/manager', ManagerController::class, ['as' => 'manager']);
    // member management
    Route::apiResource('/member', MemberController::class, ['as' => 'member']);
    // guest management
    Route::apiResource('/guest', GuestController::class, ['as' => 'guest']);
    // media management
    Route::apiResource('/media/type', MediaTypeController::class, ['as' => 'media']);
    Route::apiResource('/media/category', MediaCategoryController::class, ['as' => 'media']);
    Route::apiResource('/media/catalog', MediaController::class, ['as' => 'media']);
    // document management
    Route::apiResource('/document/type', DocumentTypeController::class, ['as' => 'document']);
    Route::apiResource('/document/category', DocumentCategoryController::class, ['as' => 'document']);
    Route::apiResource('/document/catalog', DocumentController::class, ['as' => 'document']);
    // department management
    Route::apiResource('/department/type', DepartmentTypeController::class, ['as' => 'department']);
    Route::apiResource('/department/category', DepartmentCategoryController::class, ['as' => 'department']);
    Route::apiResource('/department/catalog', DepartmentController::class, ['as' => 'department']);
    // service management
    Route::apiResource('/service/type', ServiceTypeController::class, ['as' => 'service']);
    Route::apiResource('/service/category', ServiceCategoryController::class, ['as' => 'service']);
    Route::apiResource('/service/catalog', ServiceController::class, ['as' => 'service']);
    // activity management
    Route::apiResource('/activity/type', ActivityTypeController::class, ['as' => 'activity']);
    Route::apiResource('/activity/category', ActivityCategoryController::class, ['as' => 'activity']);
    Route::apiResource('/activity/catalog', ActivityController::class, ['as' => 'activity']);
    // currency management
    Route::apiResource('/currency/type', CurrencyTypeController::class, ['as' => 'currency']);
    Route::apiResource('/currency/category', CurrencyCategoryController::class, ['as' => 'currency']);
    Route::apiResource('/currency/catalog', CurrencyController::class, ['as' => 'currency']);
    // exchange rate management
    Route::apiResource('/exchange/rate', ExchangeRateController::class, ['as' => 'exchange.rate']);
    // account management
    Route::apiResource('/account/type', AccountTypeController::class, ['as' => 'account']);
    Route::apiResource('/account/category', AccountCategoryController::class, ['as' => 'account']);
    Route::apiResource('/account/catalog', AccountController::class, ['as' => 'account']);
    // receipt management
    Route::apiResource('/receipt/type', ReceiptTypeController::class, ['as' => 'receipt']);
    Route::apiResource('/receipt/category', ReceiptCategoryController::class, ['as' => 'receipt']);
    
    // activity notifications management
    Route::prefix('notifications')->group(function () {
        Route::get('/', [ActivityNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/unread-count', [ActivityNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::post('/mark-all-read', [ActivityNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/{activityNotification}', [ActivityNotificationController::class, 'show'])->name('notifications.show');
        Route::put('/{activityNotification}/read', [ActivityNotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::put('/{activityNotification}', [ActivityNotificationController::class, 'update'])->name('notifications.update');
        Route::delete('/{activityNotification}', [ActivityNotificationController::class, 'destroy'])->name('notifications.destroy');
    });
    Route::apiResource('/receipt/catalog', ReceiptController::class, ['as' => 'receipt']);
    // invoice management
    Route::apiResource('/invoice/type', InvoiceTypeController::class, ['as' => 'invoice']);
    Route::apiResource('/invoice/category', InvoiceCategoryController::class, ['as' => 'invoice']);
    Route::apiResource('/invoice/catalog', InvoiceController::class, ['as' => 'invoice']);
    // transaction management
    Route::apiResource('/transaction/type', TransactionTypeController::class, ['as' => 'transaction']);
    Route::apiResource('/transaction/category', TransactionCategoryController::class, ['as' => 'transaction']);
    Route::apiResource('/transaction/catalog', TransactionController::class, ['as' => 'transaction']);
    // journal management
    Route::apiResource('/journal', JournalController::class, ['as' => 'journal']);
    // ledger management
    Route::apiResource('/ledger', LedgerController::class, ['as' => 'ledger']);
    // wallet management
    Route::apiResource('/wallet', WalletController::class, ['as' => 'wallet']);
    // feedback management
    Route::apiResource('/feedback/type', FeedbackTypeController::class, ['as' => 'feedback']);
    Route::apiResource('/feedback/category', FeedbackCategoryController::class, ['as' => 'feedback']);
    Route::apiResource('/feedback/catalog', FeedbackController::class, ['as' => 'feedback']);
    // ticket management
    Route::apiResource('/ticket/type', TicketTypeController::class, ['as' => 'ticket']);
    Route::apiResource('/ticket/category', TicketCategoryController::class, ['as' => 'ticket']);
    Route::apiResource('/ticket/catalog', TicketController::class, ['as' => 'ticket']);
    // communication management
    Route::apiResource('/communication/type', CommunicationTypeController::class, ['as' => 'communication']);
    Route::apiResource('/communication/category', CommunicationCategoryController::class, ['as' => 'communication']);
    Route::apiResource('/outbound/bulk/text/message/catalog', OutboundBulkTextMessageController::class, ['as' => 'outbound.bulk.text.message']);
    Route::apiResource('/outbound/bulk/voice/message/catalog', OutboundBulkVoiceMessageController::class, ['as' => 'outbound.bulk.voice.message']);
    Route::apiResource('/outbound/bulk/email/message/catalog', OutboundBulkEmailMessageController::class, ['as' => 'outbound.bulk.email.message']);
    Route::apiResource('/outbound/text/message/catalog', OutboundTextMessageController::class, ['as' => 'outbound.text.message']);
    Route::apiResource('/outbound/voice/message/catalog', OutboundVoiceMessageController::class, ['as' => 'outbound.voice.message']);
    Route::apiResource('/outbound/email/message/catalog', OutboundEmailMessageController::class, ['as' => 'outbound.email.message']);
    Route::apiResource('/inbound/text/message/catalog', InboundTextMessageController::class, ['as' => 'inbound.text.message']);
    Route::apiResource('/inbound/voice/message/catalog', InboundVoiceMessageController::class, ['as' => 'inbound.voice.message']);
    Route::apiResource('/inbound/email/message/catalog', InboundEmailMessageController::class, ['as' => 'inbound.email.message']);
    Route::apiResource('/communication/catalog', CommunicationController::class, ['as' => 'communication']);
});
