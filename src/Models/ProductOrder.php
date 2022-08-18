<?php
namespace App\Models;

class ProductOrder extends Model
{
    protected $connection = 'default';
    protected $table = 'product_order';

    public static function judgmentOrderStatus($status, $order_expired_at, $style = false)
    {
        if ($status == 'paid') {
            return ($style) ? '<span class="status status-green">完成</span>' : '完成';
        } else {
            if ($status != 'abnormal') {
                if (time() > $order_expired_at) {
                    return ($style) ? '<span class="status status-black">超时</span>' : '超时';
                } else {
                    return ($style) ? '<span class="status status-orange">等待</span>' : '等待';
                }
            } else {
                return ($style) ? '<span class="status status-red">异常</span>' : '异常';
            }
        }
    }
}
