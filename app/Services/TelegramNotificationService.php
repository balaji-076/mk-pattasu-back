<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramNotificationService
{
    private const ENDPOINT = 'https://api.telegram.org/bot%s/sendMessage';

    private readonly ?string $botToken;
    private readonly ?string $adminChatId;

    public function __construct()
    {
        $this->botToken    = config('services.telegram.bot_token');
        $this->adminChatId = config('services.telegram.chat_id');
    }

    /**
     * Deliver an HTML-formatted alert to the admin chat.
     * Never throws; returns false on any failure so callers stay unaffected.
     */
    public function notifyAdmin(string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('Telegram notification skipped: credentials not configured');

            return false;
        }

        try {
            $response = Http::timeout(5)
                ->retry(2, 200, throw: false)
                ->post(sprintf(self::ENDPOINT, $this->botToken), [
                    'chat_id'                  => $this->adminChatId,
                    'text'                     => $message,
                    'parse_mode'               => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            if ($response->failed()) {
                Log::error('Telegram notification rejected', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $e) {
            Log::error('Telegram notification failed', [
                'error' => $this->maskToken($e->getMessage()),
            ]);

            return false;
        }
    }

    private function isConfigured(): bool
    {
        return filled($this->botToken) && filled($this->adminChatId);
    }

    /**
     * Connection errors embed the request URL, so strip the bot token before logging.
     */
    private function maskToken(string $text): string
    {
        return str_replace($this->botToken, '***', $text);
    }
}