<?php

use App\Http\Middleware\EnsurePlanFeature;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'tenant.active' => EnsureTenantIsActive::class,
            'feature' => EnsurePlanFeature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // A too-large upload aborts before validation — show a friendly message instead of a 413 page.
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('messages.upload_too_large')], 413);
            }

            return back()->withErrors(['file' => __('messages.upload_too_large')]);
        });
    })->create();
