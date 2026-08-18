<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WhatsAppTestController extends Controller
{
    /**
     * Send a test message to the configured WhatsApp group via Evolution API.
     */
    public function sendTestMessage(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->is_admin, 403);

        $groupJid = $user->whatsapp_group_jid;
        abort_unless(!empty($groupJid), 422);

        $apiBaseUrl = rtrim((string) env('EVOLUTION_API_BASE_URL', ''), '/');
        $apiKey = (string) env('EVOLUTION_API_KEY', '');
        $instanceName = (string) env('EVOLUTION_INSTANCE_NAME', '');

        abort_unless($apiBaseUrl && $apiKey && $instanceName, 500);

        $text = (string) $request->input('text', 'رسالة تجريبية من MarketLink');

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$apiBaseUrl}/message/sendText/{$instanceName}", [
                    // Evolution API expects groupJid in `number` for sending to groups.
                    'number' => $groupJid,
                    'text' => $text,
                ]);

            if (! $response->successful()) {
                Log::warning('Evolution test message failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return back()->with('whatsapp-test-status', 'failed')
                    ->with('whatsapp-test-error', 'Evolution API returned an error. Check logs.');
            }

            return back()->with('whatsapp-test-status', 'sent')
                ->with('whatsapp-test-response', $response->json());
        } catch (\Throwable $e) {
            Log::error('Evolution test message exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('whatsapp-test-status', 'failed')
                ->with('whatsapp-test-error', 'Exception while contacting Evolution API.');
        }
    }
}

