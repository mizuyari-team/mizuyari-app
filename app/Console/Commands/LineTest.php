<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\LineNotifier;
use Illuminate\Console\Command;

class LineTest extends Command
{
    protected $signature = 'line:test
                            {email=test@example.com : 送信先ユーザーのメールアドレス}
                            {--message=水やり記録アプリからのテスト通知です : 送るメッセージ}';

    protected $description = 'ユーザーに登録されたLINEユーザーIDへテスト通知を送る';

    public function handle(LineNotifier $line): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('ユーザーが見つかりません: '.$this->argument('email'));
            return self::FAILURE;
        }

        if (empty($user->line_user_id)) {
            $this->error('このユーザーには line_user_id が登録されていません');
            return self::FAILURE;
        }

        if ($line->push($user->line_user_id, $this->option('message'))) {
            $this->info('送信しました');
            return self::SUCCESS;
        }

        $this->error('送信に失敗しました。storage/logs/laravel.log を確認してください');
        return self::FAILURE;
    }
}
