<?php

namespace App\Models;

class MailBlacklist extends Model
{
    protected $connection = 'default';
    protected $table = 'mail_blacklist';

    public function getCreatedAtAttribute(int $value): string
    {
        return date('Y-m-d H:i:s', $value);
    }

    public static function in(string $addr): bool
    {
        $record = self::where('addr', $addr)->first();
        return isset($record) ? true : false;
    }
}
