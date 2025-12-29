<?php

use App\Http\Middleware\EnsureEmailIsVerifiedIfRequired;
use App\Http\Middleware\EnsureRegistrationIsAllowed;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\UpdateLastSeen;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'registration.allowed' => EnsureRegistrationIsAllowed::class,
            'verified.if_required' => EnsureEmailIsVerifiedIfRequired::class,
        ]);

        $middleware->appendToGroup('web', UpdateLastSeen::class);

        RedirectIfAuthenticated::redirectUsing(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
