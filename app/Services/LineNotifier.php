<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineNotifier
{
    /**
     * 指定したLINEユーザーにテキストを送る
     */
    public function push(string $lineUserId, string $text): bool
    {
        $token = config('services.line.token');

        if (empty($token)) {
            Log::error('LINE送信失敗: LINE_CHANNEL_ACCESS_TOKEN が設定されていません');
            return false;
        }

        $response = Http::withToken($token)
            ->timeout(5)
            ->post('https://api.line.me/v2/bot/message/push', [
                'to' => $lineUserId,
                'messages' => [
                    ['type' => 'text', 'text' => $text],
                ],
            ]);

        if ($response->failed()) {
            Log::error('LINE送信失敗', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}
