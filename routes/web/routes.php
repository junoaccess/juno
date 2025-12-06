<?php

use App\Http\Controllers\Auth\AcceptInvitationController;
use App\Http\Controllers\Auth\OnboardOrganisationController;
use App\Http\Controllers\Auth\OrganizationSelectionController;
use App\Http\Controllers\Auth\OrganizationSwitchController;
use App\Http\Controllers\DocsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Root Domain Routes
|--------------------------------------------------------------------------
|
| Routes for the main/root domain (e.g., junoaccess.site)
| These handle organization selection before redirecting to subdomains.
|
*/

Route::domain(config('app.main_domain'))->group(function () {
    Route::get('/', function () {
        return Inertia::render('welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    })->name('home');

    // Documentation routes - serve VitePress static site
    Route::prefix('docs')->group(function () {
        Route::get('{any?}', DocsController::class)
            ->where('any', '.*')
            ->name('docs');
    });

    // Organization selection at root domain
    Route::get('login', [OrganizationSelectionController::class, 'show'])
        ->name('organization.select');
    Route::post('login', [OrganizationSelectionController::class, 'store'])
        ->name('organization.select.store');

    // Organisation onboarding (registration)
    Route::middleware('guest')->group(function () {
        Route::get('onboarding/organisation', [OnboardOrganisationController::class, 'show'])
            ->name('onboarding.organisation');
        Route::post('onboarding/organisation', [OnboardOrganisationController::class, 'store'])
            ->name('onboarding.organisation.store');
    });
});

/*
|--------------------------------------------------------------------------
| Subdomain Routes
|--------------------------------------------------------------------------
|
| Routes for organization subdomains (e.g., acme.junoaccess.site)
| All authenticated app functionality lives here, scoped to the organization.
|
*/

Route::domain('{organizationSlug}.'.config('app.main_domain'))
    ->middleware(['web', 'org.context'])
    ->group(function () {
        // Invitation acceptance (handles both guest registration and auth user acceptance)
        Route::get('invitations/accept/{token}', [AcceptInvitationController::class, 'show'])
            ->name('invitations.accept');
        Route::post('invitations/accept/{token}', [AcceptInvitationController::class, 'store'])
            ->name('invitations.accept.store');

        // Authenticated routes
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('/', function () {
                return redirect()->route('dashboard');
            });

            Route::get('dashboard', function () {
                return Inertia::render('dashboard');
            })->name('dashboard');

            // Organization switching (for users in multiple orgs)
            Route::get('organizations/select', [OrganizationSwitchController::class, 'index'])
                ->name('organizations.select');
            Route::post('organizations/{organization}/switch', [OrganizationSwitchController::class, 'store'])
                ->name('organizations.switch');
        });

        // Include subdomain-scoped routes
        require __DIR__.'/settings.php';
        require __DIR__.'/resources.php';
    });
