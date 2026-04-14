<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PartyController;
use App\Http\Controllers\Admin\RsvpController as AdminRsvpController;
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\Billing\StripeWebhookController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Customer\AdminController as CustomerAdminController;
use App\Http\Controllers\Customer\AccountSettingsController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\SiteSettingsController;
use App\Http\Controllers\Customer\SitePublishingController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\Staff\AccountController as StaffAccountController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\TemplateManagementController as StaffTemplateManagementController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', StripeWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('stripe.webhook');

Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/features', [MarketingController::class, 'features'])->name('marketing.features');
Route::get('/how-it-works', [MarketingController::class, 'howItWorks'])->name('marketing.how');
Route::get('/faq', [MarketingController::class, 'faq'])->name('marketing.faq');

if (app()->environment('local')) {
    Route::get('/dev/verify-email/{email}', function (string $email) {
        if (! app()->environment('local')) {
            abort(404);
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return redirect()->back()->with('status', 'No user found for that email.');
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return redirect()->intended(route('customer.dashboard', absolute: false))
            ->with('status', 'Email verified successfully (local shortcut).');
    })->name('dev.verify-email');

    Route::middleware('auth')->get('/dev/test-email', function (Request $request) {
        $user = $request->user();
        $type = $request->query('type', 'verify');

        if ($type === 'reset') {
            $token = Password::broker()->createToken($user);
            $user->sendPasswordResetNotification($token);

            return response()->json([
                'message' => 'Password reset email sent to current user.',
                'email' => $user->email,
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent to current user.',
            'email' => $user->email,
        ]);
    })->name('dev.test-email');
}

Route::middleware('tenant.resolve')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store']);
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
        Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/app', CustomerDashboardController::class)->name('customer.dashboard');
        Route::get('/app/admin', [CustomerAdminController::class, 'app'])->name('customer.admin.dashboard');
        Route::get('/app/admin/parties', fn () => app(CustomerAdminController::class)->app('parties'))->name('customer.admin.parties.page');
        Route::get('/app/admin/rsvps', fn () => app(CustomerAdminController::class)->app('rsvps'))->name('customer.admin.rsvps.page');
        Route::get('/app/admin/content', fn () => app(CustomerAdminController::class)->app('content'))->name('customer.admin.content.page');

        Route::get('/app/admin/api/dashboard', [DashboardController::class, 'stats'])->name('customer.admin.api.dashboard');
        Route::get('/app/admin/api/content', [ContentController::class, 'show'])->name('customer.admin.api.content.show');
        Route::put('/app/admin/api/content', [ContentController::class, 'update'])->name('customer.admin.api.content.update');
        Route::post('/app/admin/api/content/image', [ContentController::class, 'uploadImage'])->name('customer.admin.api.content.image');

        Route::get('/app/admin/api/parties', [PartyController::class, 'index'])->name('customer.admin.api.parties.index');
        Route::get('/app/admin/api/parties/generate-code', [PartyController::class, 'generateCode'])->name('customer.admin.api.parties.generate-code');
        Route::post('/app/admin/api/parties', [PartyController::class, 'store'])->name('customer.admin.api.parties.store');
        Route::get('/app/admin/api/parties/export', [PartyController::class, 'export'])->name('customer.admin.api.parties.export');
        Route::post('/app/admin/api/parties/import', [PartyController::class, 'import'])->name('customer.admin.api.parties.import');
        Route::post('/app/admin/api/parties/send-rsvp-emails', [PartyController::class, 'sendRsvpEmails'])->name('customer.admin.api.parties.send-rsvp-emails');
        Route::get('/app/admin/api/parties/{party}/email-history', [PartyController::class, 'emailHistory'])->name('customer.admin.api.parties.email-history');
        Route::get('/app/admin/api/parties/{party}', [PartyController::class, 'show'])->name('customer.admin.api.parties.show');
        Route::put('/app/admin/api/parties/{party}', [PartyController::class, 'update'])->name('customer.admin.api.parties.update');
        Route::delete('/app/admin/api/parties/{party}', [PartyController::class, 'destroy'])->name('customer.admin.api.parties.destroy');

        Route::post('/app/admin/api/parties/{party}/guests', [PartyController::class, 'storeGuest'])->name('customer.admin.api.guests.store');
        Route::put('/app/admin/api/guests/{guest}', [PartyController::class, 'updateGuest'])->name('customer.admin.api.guests.update');
        Route::delete('/app/admin/api/guests/{guest}', [PartyController::class, 'destroyGuest'])->name('customer.admin.api.guests.destroy');

        Route::get('/app/admin/api/rsvps', [AdminRsvpController::class, 'index'])->name('customer.admin.api.rsvps.index');
        Route::put('/app/admin/api/rsvps/{party}', [AdminRsvpController::class, 'update'])->name('customer.admin.api.rsvps.update');
        Route::get('/app/admin/api/rsvps/export', [AdminRsvpController::class, 'export'])->name('customer.admin.api.rsvps.export');

        Route::put('/app/site/publish', [SitePublishingController::class, 'update'])->name('customer.site.publish');
        Route::put('/app/site/settings', [SiteSettingsController::class, 'update'])->name('customer.site.settings.update');
        Route::post('/app/billing/checkout', [BillingController::class, 'startCheckout'])->name('billing.checkout.start');
        Route::get('/app/billing/checkout/success', [BillingController::class, 'checkoutSuccess'])->name('billing.checkout.success');
        Route::get('/app/billing/checkout/cancel', [BillingController::class, 'checkoutCancel'])->name('billing.checkout.cancel');
        Route::post('/app/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
        Route::post('/app/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
        Route::put('/app/account/profile', [AccountSettingsController::class, 'updateProfile'])->name('customer.account.profile.update');
        Route::put('/app/account/password', [AccountSettingsController::class, 'updatePassword'])->name('customer.account.password.update');
        Route::delete('/app/account', [AccountSettingsController::class, 'destroyAccount'])->name('customer.account.destroy');
    });

    Route::prefix('staff')
        ->name('staff.')
        ->middleware(['auth', 'staff.auth'])
        ->group(function () {
            Route::get('/', StaffDashboardController::class)->name('dashboard');
            Route::get('/template-management', [StaffTemplateManagementController::class, 'index'])->name('templates.index');
            Route::put('/template-management/field-help', [StaffTemplateManagementController::class, 'updateFieldHelp'])->name('templates.field-help.update');
            Route::put('/template-management/demo-source', [StaffTemplateManagementController::class, 'updateDemoSource'])->name('templates.demo-source.update');
            Route::get('/accounts', [StaffAccountController::class, 'index'])->name('accounts.index');
            Route::get('/accounts/{account}', [StaffAccountController::class, 'show'])->name('accounts.show');
            Route::put('/accounts/{account}', [StaffAccountController::class, 'update'])->name('accounts.update');
            Route::put('/accounts/{account}/sites/{site}', [StaffAccountController::class, 'updateSite'])->name('accounts.sites.update');
            Route::get('/accounts/{account}/sites/{site}/launch-admin', [StaffAccountController::class, 'launchSiteAdmin'])->name('accounts.sites.launch-admin');
        });

    Route::get('/w/{public_slug}', [PublicSiteController::class, 'showBySlug'])->name('wedding.public.legacy');
    Route::post('/w/{public_slug}/rsvp/lookup', [RsvpController::class, 'lookup'])->middleware('throttle:20,1')->name('rsvp.lookup.slug.legacy');
    Route::post('/w/{public_slug}/rsvp/{code}', [RsvpController::class, 'save'])->middleware('throttle:20,1')->name('rsvp.save.slug.legacy');

    Route::get('/demo', [PublicSiteController::class, 'demo'])->name('demo');
    Route::get('/rsvp/{code?}', [PublicSiteController::class, 'rsvp'])->name('rsvp.page');
    Route::post('/rsvp/lookup', [RsvpController::class, 'lookup'])->middleware('throttle:20,1')->name('rsvp.lookup');
    Route::post('/rsvp/{code}', [RsvpController::class, 'save'])->middleware('throttle:20,1')->name('rsvp.save');

    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'app'])->name('dashboard');
        Route::get('/parties', fn () => app(DashboardController::class)->app('parties'))->name('parties.page');
        Route::get('/rsvps', fn () => app(DashboardController::class)->app('rsvps'))->name('rsvps.page');
        Route::get('/content', fn () => app(DashboardController::class)->app('content'))->name('content.page');

        Route::get('/api/dashboard', [DashboardController::class, 'stats'])->name('api.dashboard');
        Route::get('/api/content', [ContentController::class, 'show'])->name('api.content.show');
        Route::put('/api/content', [ContentController::class, 'update'])->name('api.content.update');
        Route::post('/api/content/image', [ContentController::class, 'uploadImage'])->name('api.content.image');

        Route::get('/api/parties', [PartyController::class, 'index'])->name('api.parties.index');
        Route::get('/api/parties/generate-code', [PartyController::class, 'generateCode'])->name('api.parties.generate-code');
        Route::post('/api/parties', [PartyController::class, 'store'])->name('api.parties.store');
        Route::get('/api/parties/export', [PartyController::class, 'export'])->name('api.parties.export');
        Route::post('/api/parties/import', [PartyController::class, 'import'])->name('api.parties.import');
        Route::post('/api/parties/send-rsvp-emails', [PartyController::class, 'sendRsvpEmails'])->name('api.parties.send-rsvp-emails');
        Route::get('/api/parties/{party}/email-history', [PartyController::class, 'emailHistory'])->name('api.parties.email-history');
        Route::get('/api/parties/{party}', [PartyController::class, 'show'])->name('api.parties.show');
        Route::put('/api/parties/{party}', [PartyController::class, 'update'])->name('api.parties.update');
        Route::delete('/api/parties/{party}', [PartyController::class, 'destroy'])->name('api.parties.destroy');

        Route::post('/api/parties/{party}/guests', [PartyController::class, 'storeGuest'])->name('api.guests.store');
        Route::put('/api/guests/{guest}', [PartyController::class, 'updateGuest'])->name('api.guests.update');
        Route::delete('/api/guests/{guest}', [PartyController::class, 'destroyGuest'])->name('api.guests.destroy');

        Route::get('/api/rsvps', [AdminRsvpController::class, 'index'])->name('api.rsvps.index');
        Route::put('/api/rsvps/{party}', [AdminRsvpController::class, 'update'])->name('api.rsvps.update');
        Route::get('/api/rsvps/export', [AdminRsvpController::class, 'export'])->name('api.rsvps.export');
    });

    $publicSlugPattern = '^(?!(?:admin|api|app|demo|dev|faq|features|forgot-password|how-it-works|login|logout|pricing|register|reset-password|rsvp|staff|stripe|verify-email|w)$)[a-z0-9-]+$';

    Route::get('/{public_slug}', [PublicSiteController::class, 'showBySlug'])
        ->where('public_slug', $publicSlugPattern)
        ->name('wedding.public');
    Route::post('/{public_slug}/rsvp/lookup', [RsvpController::class, 'lookup'])
        ->where('public_slug', $publicSlugPattern)
        ->middleware('throttle:20,1')
        ->name('rsvp.lookup.slug');
    Route::post('/{public_slug}/rsvp/{code}', [RsvpController::class, 'save'])
        ->where('public_slug', $publicSlugPattern)
        ->middleware('throttle:20,1')
        ->name('rsvp.save.slug');
});
