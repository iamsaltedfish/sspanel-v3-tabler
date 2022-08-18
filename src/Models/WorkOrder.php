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
        return ($value == null) ? 'null' : date('y-m-d H:i', $value);
    }

    public function getClosedByAttribute($value)
    {
        return ($value == null) ? '<span class="status status-green">开启中</span>' : '<span class="status status-black">已关闭</span>';
    }
}
