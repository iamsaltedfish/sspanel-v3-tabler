<?php
namespace App\Models;

class MailPush extends Model
{
    protected $connection = 'default';
    protected $table = 'mail_push';

    public function allowedPush(string $item, int $user_id): bool
    {
        $config = self::where('user_id', $user_id)->first();
        return $config->$item;
    }
}
