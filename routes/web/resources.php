<?php

use App\Http\Controllers\Web\InvitationController;
use App\Http\Controllers\Web\OrganizationController;
use App\Http\Controllers\Web\PermissionController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\TeamController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('organizations', OrganizationController::class);
    Route::resource('users', UserController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('invitations', InvitationController::class);
    Route::resource('permissions', PermissionController::class)->only(['index', 'show']);
});
