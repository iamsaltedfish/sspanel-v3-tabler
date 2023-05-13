<?php

namespace App\Command;

use App\Models\DetectLog;
use App\Models\EmailQueue;
use App\Models\EmailVerify;
use App\Models\Ip;
use App\Models\MailPush;
use App\Models\Node;
use App\Models\NodeInfoLog;
use App\Models\NodeOnlineLog;
use App\Models\PasswordReset;
use App\Models\Setting;
use App\Models\Statistics as StatisticsModel;
use App\Models\StreamMedia;
use App\Models\Token;
use App\Models\User;
use App\Models\UserSubscribeLog;
use App\Services\Mail;
use App\Utils\DatatablesHelper;
use App\Utils\Tools;
use Carbon\Carbon;
use Dariuszp\CliProgressBar;
use Exception;

class Job extends Command
{
    public $description = ''
        . '├─=: php xcat Job [选项]' . PHP_EOL
        . '│ ├─ SendMail                - 处理邮件队列' . PHP_EOL
        . '│ ├─ DailyJob                - 每日任务，每天' . PHP_EOL
        . '│ ├─ CheckJob                - 检查任务，每分钟' . PHP_EOL
        . '│ ├─ UserJob                 - 用户账户相关任务，每小时' . PHP_EOL;

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

    public function SendMail()
    {
        if (file_exists(BASE_PATH . '/storage/email_queue')) {
            echo "程序正在运行中" . PHP_EOL;
            return false;
        }
        $myfile = fopen(BASE_PATH . '/storage/email_queue', 'wb+') or die('Unable to open file!');
        $txt = '1';
        fwrite($myfile, $txt);
        fclose($myfile);
        // 分块处理，节省内存
        EmailQueue::chunkById(1000, static function ($email_queues) {
            foreach ($email_queues as $email_queue) {
                try {
                    Mail::send(
                        $email_queue->to_email,
                        $email_queue->subject,
                        $email_queue->template,
                        $email_queue->mail_type ?? 'system',
                        json_decode($email_queue->array),
                        []
                    );
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                echo '发送邮件至 ' . $email_queue->to_email . PHP_EOL;
                $email_queue->delete();
            }
        });
        unlink(BASE_PATH . '/storage/email_queue');
    }

    public function DailyJob()
    {
        ini_set('memory_limit', '-1');

        // 清理各表记录
        $limit = date('Y-m-d H:i:s', time() - 86400 * (int) $_ENV['subscribeLog_keep_days']);
        //Ip::where('datetime', '<', time() - 300)->delete();
        Token::where('expire_time', '<', time())->delete();
        DetectLog::where('datetime', '<', time() - 86400 * 1)->delete();
        NodeInfoLog::where('log_time', '<', time() - 86400 * 1)->delete();
        StreamMedia::where('created_at', '<', time() - 86400 * 24)->delete();
        EmailVerify::where('expire_in', '<', time() - 86400 * 3)->delete();
        PasswordReset::where('expire_time', '<', time() - 86400 * 3)->delete();
        NodeOnlineLog::where('log_time', '<', time() - 86400 * 1)->delete();
        UserSubscribeLog::where('request_time', '<', $limit)->delete();
        StatisticsModel::where('item', 'user_traffic')->where('created_at', '<', time() - 86400 * 14)->delete();
        StatisticsModel::where('item', 'node_traffic')->where('created_at', '<', time() - 86400 * 31)->delete();
        StatisticsModel::where('item', 'node_traffic_log')->where('created_at', '<', time() - 86400 * 2)->delete();

        // 重置自增ID
        $db = new DatatablesHelper();
        Tools::resetAutoIncrement($db, 'node_online_log');
        Tools::resetAutoIncrement($db, 'node_info');

        // 记录流量用量
        $lastday_total = 0;
        $insert_data = [];
        $users = User::where('enable', '1')->get(['id', 'u', 'd', 'last_day_t']);
        foreach ($users as $user) {
            // 计算差
            $diff = $user->u + $user->d - $user->last_day_t;
            // 累加总用量
            $lastday_total += $diff;
            // 记录单用量
            $insert_data[] = [
                'item' => 'user_traffic',
                'value' => round($diff / 1048576, 2), // to mb
                'user_id' => $user->id,
                'created_at' => time(),
            ];
            // 重置统计字段
            $user->last_day_t = ($user->u + $user->d);
            $user->save();
        }

        // 批量插入
        $chunk_insert_data = array_chunk($insert_data, 10000);
        foreach ($chunk_insert_data as $data) {
            StatisticsModel::insert($data);
        }

        $traffic = new StatisticsModel();
        $traffic->item = 'traffic';
        $traffic->value = round($lastday_total / 1073741824, 2); // to gb
        $traffic->created_at = time();
        $traffic->save();

        // 记录节点流量用量
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $before_usage = StatisticsModel::where('node_id', $node->id)
                ->where('item', 'node_traffic_log')
                ->where('created_at', '>', time() - 86400)
                ->orderBy('id', 'desc')
                ->first();

            $before_usage_v = !isset($before_usage) ? null : (int) $before_usage->value;

            $traffic = new StatisticsModel();
            $traffic->item = 'node_traffic_log';
            $traffic->value = $node->node_bandwidth;
            $traffic->node_id = $node->id;
            $traffic->created_at = time();
            $traffic->save();

            if ($before_usage_v !== null) {
                $usage = new StatisticsModel();
                $usage->item = 'node_traffic';
                $yesterday_usage = round(($node->node_bandwidth - $before_usage_v) / 1048576, 2); // to mb
                if ($yesterday_usage > 0) {
                    $usage->value = $yesterday_usage;
                } else {
                    // 如果昨天是重置日
                    if ($node->bandwidthlimit_resetday === (date('j') - 1)) {
                        $usage->value = round($node->node_bandwidth / 1048576, 2); // to mb
                    } else {
                        // 如果昨天不是重置日, 但 yesterday_usage 是负数, 那就是在后台将节点流量用量改小了
                        $usage->value = 0;
                    }
                }
                $usage->node_id = $node->id;
                $usage->created_at = time();
                $usage->save();
            }
        }

        // 重置节点流量
        Node::where('bandwidthlimit_resetday', date('d'))->update(['node_bandwidth' => 0]);

        // 更新 IP 库
        if (date('d') === '1' || date('d') === '10' || date('d') === '20') {
            (new Tool($this->argv))->initQQWry();
        }

        echo 'All Done.' . PHP_EOL;
    }

    public function CheckJob()
    {
        //更新节点 IP，每分钟
        $nodes = Node::get();
        foreach ($nodes as $node) {
            $server = $node->getOutAddress();
            if (!Tools::isIp($server) && $node->changeNodeIp($server)) {
                $node->save();
            }
        }

        if ((int) date('i') % 5 === 0) {
            Ip::where('datetime', '<', time() - 300)->delete();
        }
    }

    public function UserJob()
    {
        $time_interval = [3, 0, -3]; // 分别在到期的前多少天发送邮件提醒 填负数就是在到期后的第几天发送邮件提醒
        foreach ($time_interval as $interval) {
            if (Setting::obtain('mail_driver') === 'none') {
                echo "This feature is not available because no mail sending configuration is configured." . PHP_EOL;
                break;
            }
            // https://9iphp.com/web/laravel/php-datetime-package-carbon.html
            $from = date("Y-m-d H:00:00", strtotime(Carbon::now()->addDays($interval)->toDateTimeString()));
            $to = date("Y-m-d H:00:00", strtotime(Carbon::now()->addDays($interval)->addHours(1)->toDateTimeString()));
            // https://stackoverflow.com/questions/33361628/how-to-query-between-two-dates-using-laravel-and-eloquent
            $users = User::whereBetween('expire_in', [$from, $to])->get();
            if ($users->count() === 0) {
                continue;
            }
            $text = ($interval > 0) ? "您的账户还有 {$interval} 天就要到期了，" : "您的账户已经到期 " . abs($interval) . " 天了，";
            if ($interval === 0) {
                $text = '';
            }

            /* var_dump(($interval > 0));
            echo $from . PHP_EOL . $to . PHP_EOL . $interval . PHP_EOL . '-------' . PHP_EOL; */

            // 进度条
            $bar = new CliProgressBar($users->count());
            $bar->setBarLength(50);
            $bar->display();
            $bar->setColorToYellow();

            foreach ($users as $user) {
                $bar->progress();
                if (MailPush::allow('due_reminder', $user->id)) {
                    $mail_baseUrl = $_ENV['mail_baseUrl'];
                    $unsub_link = $_ENV['mail_baseUrl'] . '/mail/push/' . $user->getMailUnsubToken();
                    $user->sendMail(
                        $_ENV['appName'] . ' - 服务到期提醒',
                        'notice.tpl',
                        'due_reminder',
                        [
                            'title' => (time() > strtotime($user->expire_in)) ? '您的服务已到期' : '您的服务即将到期',
                            'content' => $text . '为避免影响您的正常使用，建议您及时在商店选购适合您需求的商品。如果需要帮助，可以通过工单系统，或在用户中心右下角在线对话小组件与我们沟通'
                            . "<p>当前的用户中心地址是 <a href=\"{$mail_baseUrl}\">{$mail_baseUrl}</a></p>",
                            'concluding_remarks' => "此邮件由系统自动发送。取消此类通知推送，请前往 <a href=\"{$unsub_link}\">邮件推送</a> 页面设置",
                        ],
                        []
                    );
                }
            }

            $bar->setColorToGreen();
            $bar->display();
            $bar->end();
        }
    }
}
