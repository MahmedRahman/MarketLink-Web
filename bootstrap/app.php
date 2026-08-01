<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verify.github.webhook' => \App\Http\Middleware\VerifyGitHubWebhook::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'employee.work_hub' => \App\Http\Middleware\EnsureEmployeeWorkHubAdmin::class,
        ]);

        // Cloudflare / reverse proxy يمرّر X-Forwarded-Proto
        $middleware->trustProxies(at: '*');
        
        // استثناء API routes من CSRF protection
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // جلسة/CSRF منتهية (419) → رجّع لصفحة الدخول بدل "Page Expired"
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'انتهت الجلسة، سجّل الدخول مرة أخرى.',
                    'redirect' => route('login'),
                ], 419);
            }

            return redirect()
                ->guest(route('login'))
                ->with('status', 'انتهت الجلسة، سجّل الدخول مرة أخرى.');
        });

        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() !== 419) {
                return $response;
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'انتهت الجلسة، سجّل الدخول مرة أخرى.',
                    'redirect' => route('login'),
                ], 419);
            }

            return redirect()
                ->guest(route('login'))
                ->with('status', 'انتهت الجلسة، سجّل الدخول مرة أخرى.');
        });
    })->create();
