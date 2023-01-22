<?php

namespace App\Command;

use App\Models\MailStatistics;
use App\Models\ProductOrder;
use App\Models\Statistics as StatisticsModel;
use App\Models\User;
use App\Services\Analytics;

class Statistics extends Command
{
    public $description = ''
        . '├─=: php xcat Statistics [选项]' . PHP_EOL
        . '│ ├─ CheckIn                 - 统计签到数' . PHP_EOL
        . '│ ├─ Another                 - 统计其他项目' . PHP_EOL
        . '│ ├─ CountHistoryRegister    - 统计历史注册数' . PHP_EOL
        . '│ ├─ CountHistoricalSales    - 统计历史销售额' . PHP_EOL;

    public function boot()
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在.' . PHP_EOL;
            }
        }
    }

    public function CheckIn()
    {
        // 记录每日签到用户数
        $sts = new Analytics();
        $check_in = new StatisticsModel();
        $check_in->item = 'checkin';
        $check_in->value = $sts->getTodayCheckinUser();
        $check_in->created_at = time();
        $check_in->save();

        echo 'Counting the number of check-in users has been completed.' . PHP_EOL;
    }

    public function Another()
    {
        $stop = date('Y-m-d 00:00:00', strtotime("-0 day"));
        $start = date('Y-m-d 00:00:00', strtotime("-1 day"));

        $log = new StatisticsModel();
        $log->item = 'register';
        $log->value = (int) User::whereBetween('reg_date', [$start, $stop])->count();
        $log->created_at = strtotime($stop);
        $log->save();
        echo 'Count of registered users completed yesterday.' . PHP_EOL;

        $order_amount = ProductOrder::whereBetween('created_at', [strtotime($start), strtotime($stop)])
            ->where('product_type', '!=', 'recharge')
            ->sum('order_price');
        $deal_amount = ProductOrder::where('order_status', 'paid')
            ->whereBetween('created_at', [strtotime($start), strtotime($stop)])
            ->where('product_type', '!=', 'recharge')
            ->sum('order_price');

        $log = new StatisticsModel();
        $log->item = 'order_amount';
        $log->value = $order_amount / 100;
        $log->created_at = strtotime($stop);
        $log->save();

        $log = new StatisticsModel();
        $log->item = 'deal_amount';
        $log->value = $deal_amount / 100;
        $log->created_at = strtotime($stop);
        $log->save();
        echo "Count yesterday's order amount has been completed." . PHP_EOL;

        // 统计邮件
        $mail_data = MailStatistics::whereBetween('created_at', [strtotime($start), strtotime($stop)])->get();
        $count = [
            'basic' => $mail_data->where('type', 'basic')->count(),
            'system' => $mail_data->where('type', 'system')->count(),
            'market' => $mail_data->where('type', 'market')->count(),
            'work_order' => $mail_data->where('type', 'work_order')->count(),
            'due_reminder' => $mail_data->where('type', 'due_reminder')->count(),
            'traffic_report' => $mail_data->where('type', 'traffic_report')->count(),
            'general_notice' => $mail_data->where('type', 'general_notice')->count(),
            'important_notice' => $mail_data->where('type', 'important_notice')->count(),
            'account_security' => $mail_data->where('type', 'account_security')->count(),
        ];

        $mail = new StatisticsModel();
        $mail->item = 'mail_count';
        $mail->value = json_encode($count);
        $mail->created_at = strtotime($stop);
        $mail->save();
        echo "The statistics of yesterday s email sending have been completed." . PHP_EOL;
    }

    public function CountHistoryRegister()
    {
        $day_limit = 90;

        for ($i = 0; $i <= $day_limit; $i++) {
            if ($i !== 0) {
                $stop_day = $i - 1;
                $start = date('Y-m-d 00:00:00', strtotime("-{$i} day"));
                $stop = date('Y-m-d 00:00:00', strtotime("-{$stop_day} day"));
                $number = User::whereBetween('reg_date', [$start, $stop])->count();

                $log = new StatisticsModel();
                $log->item = 'register';
                $log->value = $number;
                $log->created_at = strtotime($stop);
                $log->save();
            }
        }
    }

    public function CountHistoricalSales()
    {
        $day_limit = 30;

        for ($i = 0; $i <= $day_limit; $i++) {
            if ($i !== 0) {
                $stop_day = $i - 1;
                $start = strtotime(date('Y-m-d 00:00:00', strtotime("-{$i} day")));
                $stop = strtotime(date('Y-m-d 00:00:00', strtotime("-{$stop_day} day")));
                $order_amount = ProductOrder::whereBetween('created_at', [$start, $stop])
                    ->where('product_type', '!=', 'recharge')
                    ->sum('order_price');
                $deal_amount = ProductOrder::where('order_status', 'paid')
                    ->whereBetween('created_at', [$start, $stop])
                    ->where('product_type', '!=', 'recharge')
                    ->sum('order_price');

                $log = new StatisticsModel();
                $log->item = 'order_amount';
                $log->value = $order_amount / 100;
                $log->created_at = $stop;
                $log->save();

                $log = new StatisticsModel();
                $log->item = 'deal_amount';
                $log->value = $deal_amount / 100;
                $log->created_at = $stop;
                $log->save();
            }
        }
    }
}
