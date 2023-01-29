<?php

namespace App\Models;

class InviteCode extends Model
{
    protected $connection = 'default';
    protected $table = 'user_invite_code';

    public static function invitationPermissionCheck($user_id): bool
    {
        if ($_ENV['registration_duration_switch'] === false && $_ENV['consumption_amount_switch'] === false) {
            return true;
        }
        $user = User::find($user_id);
        if ($_ENV['registration_duration_switch']) {
            $seconds = $_ENV['registration_duration'] * 86400;
            if (strtotime($user->reg_date) + $seconds > time()) {
                return false;
            }
        }
        if ($_ENV['consumption_amount_switch']) {
            $consumption = ProductOrder::where('order_status', 'paid')
                ->where('product_type', '!=', 'recharge')
                ->where('user_id', $user->id)
                ->sum('order_price');
            if ($_ENV['consumption_amount'] * 100 > $consumption) {
                return false;
            }
        }
        return true;
    }
}
