<?php

namespace App\Models;

class ProductOrder extends Model
{
    protected $connection = 'default';
    protected $table = 'product_order';

    public static function judgmentOrderStatus($status, $order_expired_at, $style = false)
    {
        if ($status === 'paid') {
            return ($style) ? '<span class="status status-green">完成</span>' : '完成';
        }
        if ($status === 'refunded') {
            return ($style) ? '<span class="status status-purple">已退</span>' : '已退';
        }
        if ($status !== 'abnormal') {
            if (time() > $order_expired_at) {
                return ($style) ? '<span class="status status-black">超时</span>' : '超时';
            } else {
                return ($style) ? '<span class="status status-orange">等待</span>' : '等待';
            }
        } else {
            return ($style) ? '<span class="status status-red">异常</span>' : '异常';
        }
    }

    public static function translateOrderStatus(string $status, int $order_expired_at): string
    {
        if ($status === 'paid') {
            return '已支付';
        }
        if ($status === 'refunded') {
            return '已退款';
        }
        if ($status !== 'abnormal') {
            if (time() > $order_expired_at) {
                return '超时';
            } else {
                return '等待支付';
            }
        } else {
            return '异常';
        }
    }
}
