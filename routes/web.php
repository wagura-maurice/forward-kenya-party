<?php
// routes/web.php
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\OtpVerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', [FrontendController::class, 'welcome'])->name('frontend.welcome');
Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('frontend.about-us');
Route::get('/contact-us', [FrontendController::class, 'contactUs'])->name('frontend.contact-us');

Route::get('/services', [FrontendController::class, 'services'])->name('frontend.services');
Route::get('/service/{id}/show', [FrontendController::class, 'showService'])->name('frontend.show.service');
Route::get('/departments', [FrontendController::class, 'departments'])->name('frontend.departments');
Route::get('/department/{id}/show', [FrontendController::class, 'showDepartment'])->name('frontend.show.department');

Route::get('/help-and-support', [FrontendController::class, 'helpSupport'])->name('frontend.help-and-support');
Route::get('/frequently-asked-questions', [FrontendController::class, 'frequentlyAskedQuestions'])->name('frontend.frequently-asked-questions');
Route::get('/terms-and-conditions', [FrontendController::class, 'termsConditions'])->name('frontend.terms-and-conditions');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('frontend.privacy-policy');

// Platform Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', [BackendController::class, 'dashboard'])->name('dashboard');
    
    // Dynamic department and service routes are registered in RouteServiceProvider
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

// OTP Verification Routes
Route::post('/otp/send', [OtpVerificationController::class, 'sendOtp'])->name('otp.send');
Route::post('/otp/verify', [OtpVerificationController::class, 'verifyOtp'])->name('otp.verify');
