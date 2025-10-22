<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Process as SymfonyProcess;

class WebhookController extends Controller
{
    /**
     * Handle GitHub webhook for auto-deployment
     */
    public function github(Request $request)
    {
        // Verify GitHub signature
        if (!$this->verifyGitHubSignature($request)) {
            Log::warning('Invalid GitHub webhook signature');
            return response('Unauthorized', 401);
        }

        $payload = json_decode($request->getContent(), true);
        
        // Check if this is a push to main/master branch
        if ($payload['ref'] !== 'refs/heads/main' && $payload['ref'] !== 'refs/heads/master') {
            Log::info('Webhook received for non-main branch: ' . $payload['ref']);
            return response('OK - Non-main branch', 200);
        }

        // Log the deployment start
        Log::info('Starting auto-deployment from GitHub webhook', [
            'commit' => $payload['head_commit']['id'] ?? 'unknown',
            'message' => $payload['head_commit']['message'] ?? 'unknown',
            'author' => $payload['head_commit']['author']['name'] ?? 'unknown'
        ]);

        try {
            // Run deployment script
            $this->deploy();
            
            Log::info('Auto-deployment completed successfully');
            return response('Deployment successful', 200);
            
        } catch (\Exception $e) {
            Log::error('Auto-deployment failed: ' . $e->getMessage());
            return response('Deployment failed', 500);
        }
    }

    /**
     * Verify GitHub webhook signature
     */
    private function verifyGitHubSignature(Request $request)
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

    /**
     * Execute deployment commands
     */
    private function deploy()
    {
        $deploymentPath = base_path();
        
        // Change to project directory
        chdir($deploymentPath);
        
        // Git pull latest changes
        $this->runCommand('git pull origin main');
        
        // Install/update Composer dependencies
        $this->runCommand('composer install --no-dev --optimize-autoloader');
        
        // Run Laravel optimizations
        $this->runCommand('php artisan config:cache');
        $this->runCommand('php artisan route:cache');
        $this->runCommand('php artisan view:cache');
        
        // Run database migrations (if any)
        $this->runCommand('php artisan migrate --force');
        
        // Clear application cache
        $this->runCommand('php artisan cache:clear');
        
        // Restart queue workers (if using queues)
        $this->runCommand('php artisan queue:restart');
        
        Log::info('Deployment commands executed successfully');
    }

    /**
     * Run shell command safely
     */
    private function runCommand($command)
    {
        Log::info("Executing command: {$command}");
        
        $process = new SymfonyProcess($command);
        $process->setTimeout(300); // 5 minutes timeout
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new \Exception("Command failed: {$command}\nError: " . $process->getErrorOutput());
        }
        
        Log::info("Command completed: {$command}\nOutput: " . $process->getOutput());
    }

    /**
     * Get deployment status
     */
    public function status()
    {
        return response()->json([
            'status' => 'active',
            'last_deployment' => now()->toISOString(),
            'environment' => app()->environment(),
            'version' => config('app.version', '1.0.0')
        ]);
    }
}
