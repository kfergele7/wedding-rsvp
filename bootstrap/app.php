<?php

use App\Http\Middleware\EnsureAdminAuthenticated;
use App\Http\Middleware\EnsureStaffUser;
use App\Http\Middleware\ResolveTenantContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth' => EnsureAdminAuthenticated::class,
            'staff.auth' => EnsureStaffUser::class,
            'tenant.resolve' => ResolveTenantContext::class,
        ]);
        $middleware->redirectUsersTo('/app');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
