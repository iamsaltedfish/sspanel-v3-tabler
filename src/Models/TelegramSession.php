<?php

namespace App\Models;

use App\Utils\Tools;

class TelegramSession extends Model
{
    protected $connection = 'default';
    protected $table = 'telegram_session';

    public static function generateToken(int $user_id): string
    {
        $text = Tools::genRandomChar(16);
        $record = new TelegramSession();
        $record->type = 0; // ?
        $record->user_id = $user_id;
        $record->datetime = time();
        $record->session_content = $text;
        $record->save();
        return $text;
    }

    public static function isAValidToken(string $token): array
    {
        $result = [];
        $record = TelegramSession::where('session_content', $token)
            ->where('datetime', '>', time() - 300) // 五分钟
            ->orderBy('id', 'desc')
            ->first();

        $result['bool'] = isset($record) ? true : false;
        if ($result['bool']) {
            $result['user_id'] = $record->user_id;
        }
        return $result;
    }
}
