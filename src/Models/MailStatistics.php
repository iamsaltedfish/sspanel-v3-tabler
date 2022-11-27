<?php
namespace App\Models;

class MailStatistics extends Model
{
    protected $connection = 'default';
    protected $table = 'mail_statistics';

    public function getCreatedAtAttribute(int $value): string
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getStatusAttribute(int $value): string
    {
        switch ($value) {
            case 0:
                return '正常';
            case 1:
                return '用户拒收';
            case 2:
                return '黑名单中';
        }
    }
}
