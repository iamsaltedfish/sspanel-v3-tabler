<?php

namespace App\Models;

class InviteCode extends Model
{
    protected $connection = 'default';
    protected $table = 'user_invite_code';

    public static function judgeRegistrationDuration(object $user): array
    {
        $check = [];
        $check['reason'] = null;
        $check['result'] = false;
        // 如果限制注册时长且用户不满足 返回假
        if ($_ENV['registration_duration_switch']) {
            $seconds = $_ENV['registration_duration'] * 86400;
            if (strtotime($user->reg_date) + $seconds > time()) {
                $wait_day = round((strtotime($user->reg_date) + $seconds - time()) / 86400);
                $check['reason'] = "注册时间未满足要求，还需等待约 {$wait_day} 天";
                return $check;
            }
        }
        // 满足返回真
        $check['result'] = true;
        return $check;
    }

    public static function judgingTotalConsumption(object $user): array
    {
        $check = [];
        $check['reason'] = null;
        $check['result'] = false;
        // 如果限制消费金额且用户不满足 返回假
        if ($_ENV['consumption_amount_switch']) {
            $consumption = ProductOrder::where('order_status', 'paid')
                ->where('product_type', '!=', 'recharge')
                ->where('user_id', $user->id)
                ->sum('order_price');

            if ($_ENV['consumption_amount'] * 100 > $consumption) {
                $amount = ($_ENV['consumption_amount'] * 100 - $consumption) / 100;
                $check['reason'] = "消费金额未满足要求，还需消费 {$amount} 元";
                return $check;
            }
        }
        // 满足返回真
        $check['result'] = true;
        return $check;
    }

    public static function invitationPermissionCheck($user_id): array
    {
        $end = [];
        $end['reason'] = null;
        $end['result'] = false;
        $pick_one_of_two = $_ENV['one_of_the_conditions_is_satisfied'] ?? false;
        // 如果对两个条件都没有限制 返回真
        if ($_ENV['registration_duration_switch'] === false && $_ENV['consumption_amount_switch'] === false) {
            $end['result'] = true;
            return $end;
        }
        $user = User::find($user_id);
        $check_reg_time = self::judgeRegistrationDuration($user);
        $check_pay_sum = self::judgingTotalConsumption($user);
        if ($pick_one_of_two) {
            if ($check_reg_time['result'] || $check_pay_sum['result']) {
                $end['result'] = true;
                return $end;
            }
        }
        if ($check_reg_time['result'] === false || $check_pay_sum['result'] === false) {
            $end['result'] = false;
            $end['reason'] = isset($check_reg_time['reason']) ? $check_reg_time['reason'] : $check_pay_sum['reason'];
            return $end;
        }
        // 都满足返回真
        $end['result'] = true;
        return $end;
    }
}
