<?php

use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('invitations', InvitationController::class);
    Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);
});
