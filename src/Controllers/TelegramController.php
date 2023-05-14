<?php

namespace App\Controllers;

use App\Models\Link;
use App\Models\TelegramSession;
use App\Models\User;
use App\Utils\Tools;
use TelegramBot\Api\Client;

class TelegramController
{
    public static function requestEntry(array $body)
    {
        $bot = new Client($_ENV['telegram_token']);
        if (isset($body['message'])) {
            $text = $body['message']['text'];
            $chat_id = $body['message']['chat']['id'];
            try {
                $arr = explode(' ', $text);
                // 绑定账户
                if ($arr[0] === '/start') {
                    if (!isset($arr[1])) {
                        $bot->sendMessage($chat_id, "绑定失败，没有传入令牌");
                        return;
                    }
                    $query = TelegramSession::isAValidToken($arr[1]);
                    if ($query['bool']) {
                        $user = User::find($query['user_id']);
                        $user->im_type = 4;
                        $user->telegram_id = $chat_id;
                        $user->im_value = $body['message']['from']['username'];
                        $user->save();
                        $bot->sendMessage($chat_id, "绑定成功，您的注册账户是: {$user->email}");
                    } else {
                        $bot->sendMessage($chat_id, "绑定失败，可能是令牌已过期。请刷新页面后重试");
                    }
                    return;
                }
                // 查询用户
                $user = User::where('telegram_id', $chat_id)->first();
                if (!isset($user)) {
                    $bot->sendMessage($chat_id, "尚未关联到账户。请在 *我的->资料修改->其他* 选项卡中，点击相应按钮绑定您的账户", 'Markdown');
                    return;
                }
                // 签到
                if ($arr[0] === '/checkin') {
                    $bot->sendMessage($chat_id, self::userCheckIn($user));
                    return;
                }
                // 获取订阅地址
                if ($arr[0] === '/sublink') {
                    $sub_token = Link::where('userid', $user->id)->first();
                    $sub_link = $_ENV['subUrl'] . $sub_token->token;
                    $text = "以下是你的订阅地址（点击即可复制）：\n";
                    $text .= "V2rayN 系列: \n`{$sub_link}`\n";
                    $text .= "Clash 系列: \n`{$sub_link}?clash=1`\n";
                    $text .= "Quantumult: \n`{$sub_link}?quantumult=1`\n";
                    $bot->sendMessage($chat_id, $text, 'Markdown');
                    return;
                }
                // 查询账户资料
                if ($arr[0] === '/profile') {
                    $text = "您当前的账户是: {$user->email}\n";
                    $text .= "您当前账户余额为：{$user->money}\n";
                    if (time() > strtotime($user->expire_in)) {
                        $text .= "您的账户已经过期了\n";
                    } else {
                        $diff = round((strtotime($user->expire_in) - time()) / 86400);
                        $text .= "您的账户还有 {$diff} 天过期\n";
                    }
                    $text .= "您今天用了 {$user->todayusedTraffic()} 流量，还可以用 {$user->unusedTraffic()}\n";
                    $bot->sendMessage($chat_id, $text, 'Markdown');
                    return;
                }
                if ($arr[0] === '/help') {
                    $text = "以下是当前支持的命令列表：\n";
                    $text .= "`/checkin ` - 签到\n";
                    $text .= "`/sublink ` - 获取订阅地址\n";
                    $text .= "`/profile ` - 查询账户信息\n";
                    $text .= "更多功能请前往用户中心操作\n";
                    $bot->sendMessage($chat_id, $text, 'Markdown');
                    return;
                }
                $bot->sendMessage($chat_id, '今天有什么可以帮您? 发送 `/help` 获取支持的命令列表', 'Markdown');
            } catch (\Exception $e) {
                $bot->sendMessage($chat_id, $e->getMessage());
            }
        }
    }

    public static function userCheckIn(object $user): string
    {
        if ($_ENV['enable_checkin'] === false) {
            return '抱歉，管理员设置了暂时不能签到';
        }
        if ($_ENV['enable_expired_checkin'] === false && strtotime($user->expire_in) < time()) {
            return '抱歉，管理员设置了账户过期时不能签到';
        }
        if (!$user->isAbleToCheckin()) {
            return '今天已经签到过了，明天再来吧';
        }

        $rand_traffic = random_int((int) $_ENV['checkinMin'], (int) $_ENV['checkinMax']);
        $user->transfer_enable += Tools::toMB($rand_traffic);
        $user->last_check_in_time = time();
        $return_text = "签到获得了 {$rand_traffic} MB 流量";

        if ($_ENV['checkin_add_time']) {
            $add_timestamp = $_ENV['checkin_add_time_hour'] * 3600;
            if (time() > strtotime($user->expire_in)) {
                $user->expire_in = date('Y-m-d H:i:s', time() + $add_timestamp);
            } else {
                $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + $add_timestamp);
            }
            $return_text .= sprintf("和 %d 小时的使用时长", $_ENV['checkin_add_time_hour']);
        }

        $user->save();
        return $return_text;
    }

    public static function sendMessageToAdmin(string $content)
    {
        $bot = new Client($_ENV['telegram_token']);
        $admins = User::where('is_admin', 1)->get(['telegram_id']);
        if ($admins->count() > 0) {
            foreach ($admins as $admin) {
                if (isset($admin->telegram_id) && $admin->telegram_id !== '') {
                    try {
                        $bot->sendMessage($admin->telegram_id, $content, 'Markdown');
                    } catch (\Exception $e) {
                        // todo
                    }
                }
            }
        }
    }
}
