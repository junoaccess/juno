<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfGuest;
use App\Http\Middleware\RequirePasswordWithOrganization;
use App\Http\Middleware\SetCurrentOrganization;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web/routes.php',
        api: __DIR__.'/../routes/api/v1/routes.php',
        apiPrefix: 'api/v1',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            SetCurrentOrganization::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'org.context' => SetCurrentOrganization::class,
            'guest' => RedirectIfAuthenticated::class,
            'auth' => RedirectIfGuest::class,
            'password.confirm' => RequirePasswordWithOrganization::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
