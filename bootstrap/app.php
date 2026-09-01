<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'data.usage.agreement' => \App\Http\Middleware\EnsureUserAgreedToDataUsage::class,
            'check.employee.record' => \App\Http\Middleware\CheckEmployeeRecord::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Thrown by ValidatePostSize before routing/session/auth even run, so
        // Laravel's own error handling can't flash a validation message back —
        // it falls through to the raw debug/whoops page instead. Render a
        // friendly, self-contained page for it (no auth/session dependency),
        // regardless of APP_DEBUG, since this is a normal user-input problem,
        // not something a developer needs a stack trace for.
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $bytes = ini_parse_quantity(ini_get('post_max_size'));
            $maxUploadSize = $bytes > 0
                ? round($bytes / 1048576) . ' MB'
                : 'a few megabytes';

            return response()
                ->view('errors.413', compact('maxUploadSize'), 413);
        });
    })->create();
