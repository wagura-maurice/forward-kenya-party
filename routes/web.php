<?php
// routes/web.php
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\FrontendController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', [FrontendController::class, 'welcome'])->name('frontend.welcome');
Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('frontend.about-us');
Route::get('/contact-us', [FrontendController::class, 'contactUs'])->name('frontend.contact-us');

Route::get('/services', [FrontendController::class, 'services'])->name('frontend.services');
Route::get('/service/{id}/show', [FrontendController::class, 'showService'])->name('frontend.show.service');
Route::get('/departments', [FrontendController::class, 'departments'])->name('frontend.departments');
Route::get('/department/{id}/show', [FrontendController::class, 'showDepartment'])->name('frontend.show.department');

Route::get('/verify-membership', [FrontendController::class, 'verifyMembership'])->name('frontend.verify.membership');
Route::post('/verify-membership-request', [FrontendController::class, 'verifyMembershipRequest'])->name('frontend.verify.membership.request');

Route::get('/help-and-support', [FrontendController::class, 'helpSupport'])->name('frontend.help-and-support');
Route::get('/frequently-asked-questions', [FrontendController::class, 'frequentlyAskedQuestions'])->name('frontend.frequently-asked-questions');
Route::get('/terms-and-conditions', [FrontendController::class, 'termsConditions'])->name('frontend.terms-and-conditions');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('frontend.privacy-policy');

// Party Documents
Route::get('/party-ideology', [FrontendController::class, 'viewPartyIdeology'])->name('frontend.party-ideology');
Route::get('/party-manifesto', [FrontendController::class, 'viewPartyManifesto'])->name('frontend.party-manifesto');
Route::get('/party-constitution', [FrontendController::class, 'viewPartyConstitution'])->name('frontend.party-constitution');
Route::get('/party-nomination-rules', [FrontendController::class, 'viewPartyNominationRules'])->name('frontend.party-nomination-rules');

// Platform Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', [BackendController::class, 'dashboard'])->name('dashboard');
    
    // Dynamic department and service routes are registered in RouteServiceProvider

    // Profile
    Route::get('/profile', [BackendController::class, 'profile'])->name('profile');
    Route::get('/profile/{user_id}/view', [BackendController::class, 'viewProfile'])->name('profile.view');

    // Import
    Route::get('/import/membership', [ImportController::class, 'importMembership'])->name('import.membership');

    // Export
    Route::get('/export/membership', [ExportController::class, 'exportMembership'])->name('export.membership');

    // Activity
    Route::get('/activity', [BackendController::class, 'activity'])->name('activity');
    Route::delete('/activity/{id}/delete', [BackendController::class, 'deleteActivity'])->name('activity.delete');

    // Settings
    Route::get('/settings', [BackendController::class, 'settings'])->name('settings');
});

Route::get('/email/verify', function () {
    $user = Auth::user();
        
    if ($user && $user->hasVerifiedEmail()) {
        return redirect()->route('dashboard'); // Change 'dashboard' to your actual dashboard route name
    }
    
    return Inertia::render('Auth/VerifyEmail');
})->middleware(['auth:sanctum', config('jetstream.auth_session')])->name('verification.notice');
 
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/dashboard');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'signed'])->name('verification.verify');
 
Route::post('/email/verification-notification', function (Request $request) {
    try {
        $request->user()->sendEmailVerificationNotification();
 
        return back()->with('status', 'verification-link-sent');
    } catch (\Throwable $th) {
        // throw $th;
        // eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());
    }

    return back()->with('status', 'verification-link-failed');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'throttle:6,1'])->name('verification.send');

