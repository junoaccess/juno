<?php

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\OrganizationSwitchController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Public invitation routes (no auth required)
Route::get('/invitations/accept/{token}', [AcceptInvitationController::class, 'show'])
    ->name('invitations.accept');
Route::post('/invitations/accept/{token}', [AcceptInvitationController::class, 'store'])
    ->name('invitations.accept.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Organization selection and switching
    Route::get('/organizations/select', [OrganizationSwitchController::class, 'index'])
        ->name('organizations.select');
    Route::post('/organizations/{organization}/switch', [OrganizationSwitchController::class, 'store'])
        ->name('organizations.switch');
});

require __DIR__.'/settings.php';
require __DIR__.'/resources.php';
