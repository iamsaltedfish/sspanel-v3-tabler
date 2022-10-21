<?php
namespace App\Models;

class WorkOrder extends Model
{
    protected $connection = 'default';
    protected $table = 'work_order';

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getClosedAtAttribute($value)
    {
        return ($value == null) ? 'null' : date('Y-m-d H:i', $value);
    }

    public function getTheLatestReply($tk_id): string
    {
        $reply = self::where('tk_id', $tk_id)
            ->orderBy('id', 'desc')
            ->first();
        return $reply->content;
    }

    public function getTheWorkOrderStatus($tk_id): string
    {
        $topic = self::where('is_topic', 1)
            ->where('tk_id', $tk_id)
            ->first();
        if ($topic->closed_by === null) {
            return ($topic->wait_reply === 'admin') ? 'open_wait_admin' : 'open_wait_user';
        }

        return 'closed';
    }
}
