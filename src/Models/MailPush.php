<?php
namespace App\Models;

class MailPush extends Model
{
    protected $connection = 'default';
    protected $table = 'mail_push';

    public static function allow(string $item, int $user_id): bool
    {
        if ($item === 'system') {
            return true;
        }
        $config = self::where('user_id', $user_id)->first();
        return $config->$item;
    }
}
