<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyGitHubWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only verify for webhook routes
        if (!$request->is('webhook/*')) {
            return $next($request);
        }

        // Verify GitHub signature
        if (!$this->verifyGitHubSignature($request)) {
            Log::warning('Invalid GitHub webhook signature', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);
            
            return response('Unauthorized', 401);
        }

        return $next($request);
    }

    /**
     * Verify GitHub webhook signature
     */
    private function verifyGitHubSignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');
        $payload = $request->getContent();
        $secret = config('app.github_webhook_secret');
        
        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($expectedSignature, $signature);
    }
}
