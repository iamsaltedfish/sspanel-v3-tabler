<?php

namespace App\Utils;

use App\Controllers\AuthController;
use App\Controllers\LinkController;
use App\Models\User;
use App\Services\Config;
use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TelegramProcess
{
    private static $all_rss = [
        'clean_link' => '重置订阅',
        '?mu=0' => 'SSR普通订阅',
        '?mu=1' => 'SSR单端口订阅',
        '?mu=2' => 'V2ray订阅',
        '?mu=4' => 'Clash订阅',
    ];

    private static function callbackBindMethod($bot, $callback)
    {
        $callback_data = $callback->getData();
        $message = $callback->getMessage();
        $reply_to = $message->getMessageId();
        $user = User::where('telegram_id', $callback->getFrom()->getId())->first();
        $reply_message = '???';
        if ($user !== null) {
            switch (true) {
                case strpos($callback_data, 'mu'):
                    $ssr_sub_token = LinkController::getSubscriptionToken($user->id);
                    $subUrl = $_ENV['subUrl'];
                    $reply_message = self::$all_rss[$callback_data] . ': ' . $subUrl . $ssr_sub_token . $callback_data . PHP_EOL;
                    break;
                case $callback_data === 'clean_link':
                    $user->cleanLink();
                    $reply_message = '链接重置成功';
                    break;
            }
            $bot->sendMessage($message->getChat()->getId(), $reply_message, $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
        }
    }

    private static function needbindMethod($bot, $message, $command, $user, $reply_to = null)
    {
        $reply = [
            'message' => '???',
            'markup' => null,
        ];
        if ($user !== null) {
            switch ($command) {
                case 'traffic':
                    $reply['message'] = '您当前的流量状况：
今日已使用 ' . $user->todayusedTraffic() . ' ' . number_format(($user->u + $user->d - $user->last_day_t) / $user->transfer_enable * 100, 2) . '%
今日之前已使用 ' . $user->lastUsedTraffic() . ' ' . number_format($user->last_day_t / $user->transfer_enable * 100, 2) . '%
未使用 ' . $user->unusedTraffic() . ' ' . number_format(($user->transfer_enable - ($user->u + $user->d)) / $user->transfer_enable * 100, 2) . '%';
                    break;
                case 'checkin':
                    if ($_ENV['enable_checkin'] === false) {
                        $reply['message'] = '目前站点没有启用签到功能。';
                        break;
                    }
                    if (!$user->isAbleToCheckin()) {
                        $reply['message'] = '您今天已经签过到了！';
                        break;
                    }
                    $traffic = random_int($_ENV['checkinMin'], $_ENV['checkinMax']);
                    $user->transfer_enable += Tools::toMB($traffic);
                    $user->last_check_in_time = time();
                    $user->save();
                    $reply['message'] = '签到成功！获得了 ' . $traffic . ' MB 流量！';
                    break;
                case 'prpr':
                    $prpr = ['⁄(⁄ ⁄•⁄ω⁄•⁄ ⁄)⁄', '(≧ ﹏ ≦)', '(*/ω＼*)', 'ヽ(*。>Д<)o゜', '(つ ﹏ ⊂)', '( >  < )'];
                    $reply['message'] = $prpr[random_int(0, 5)];
                    break;
                case 'taro':
                    $reply['message'] = 'Anankke 是芋头';
                    break;
                case 'rss':
                    $reply['message'] = '点击以下按钮获取对应订阅: ';
                    $keys = [];
                    foreach (self::$all_rss as $key => $value) {
                        $keys[] = [['text' => $value, 'callback_data' => $key]];
                    }
                    $reply['markup'] = new InlineKeyboardMarkup(
                        $keys
                    );
                    break;
            }
        } else {
            $reply['message'] = '您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号';
        }

        return $reply;
    }

    public static function telegramProcess($bot, $message, $command)
    {
        $user = User::where('telegram_id', $message->getFrom()->getId())->first();
        $reply_to = $message->getMessageId();
        $reply = [
            'message' => '???',
            'markup' => null,
        ];
        if ($message->getChat()->getId() > 0) {
            //个人
            $bot->sendChatAction($message->getChat()->getId(), 'typing');

            switch ($command) {
                case 'ping':
                    $reply['message'] = 'Pong!您的 ID 是 ' . $message->getChat()->getId() . '!';
                    break;
                case 'traffic':
                case 'checkin':
                case 'prpr':
                case 'rss':
                    $reply = self::needbindMethod($bot, $message, $command, $user, $reply_to);
                    break;
                case 'help':
                    $reply['message'] = '命令列表：
						/ping  获取群组ID
						/traffic 查询流量
						/checkin 签到
						/help 获取帮助信息
						/rss 获取节点订阅';
                    break;
                default:
                    if ($message->getPhoto() === null) {
                        if (!is_numeric($message->getText()) || strlen($message->getText()) !== 6) {
                            break;
                        }

                        if ($user === null) {
                            $telegram_id = $message->getFrom()->getId();
                            $username = $message->getFrom()->getUsername();

                            $email = Tools::genRandomChar(6) . '.' . $telegram_id . '@telegram.org';
                            $passwd = Tools::genRandomChar(18); // Random Password
                            $imtype = 4; // Telegram
                            $imvalue = $username;
                            $name = $username;
                            if (!$name) {
                                $name = 'telegram.' . $telegram_id;
                            }
                            $code = 0;
                            $res = AuthController::registerHelper($name, $email, $passwd, $code, $imtype, $imvalue, $telegram_id, 'null');

                            if ($res['ret'] === 0) {
                                $reply['message'] = $res['msg'];
                                break;
                            }
                            $user = User::where('telegram_id', $telegram_id)->first();
                        }
                        if ($user !== null) {
                            $uid = TelegramSessionManager::verify_login_number($message->getText(), $user->id);
                            if ($uid !== 0) {
                                $reply['message'] = '登录验证成功，邮箱：' . $user->email;
                            } else {
                                $reply['message'] = '登录验证失败，数字无效';
                            }
                            break;
                        }

                        $reply['message'] = '登录验证失败，您未绑定本站账号';
                        break;
                    }

                    $bot->sendMessage($message->getChat()->getId(), '正在解码，请稍候。。。');
                    $bot->sendChatAction($message->getChat()->getId(), 'typing');
                    $photos = $message->getPhoto();
                    $file_path = $bot->getFile($photos[0]->getFileId())->getFilePath();
                    $qrcode_text = QRcode::decode('https://api.telegram.org/file/bot' . Config::get('telegram_token') . '/' . $file_path);

                    if (strpos($qrcode_text, 'mod://bind/') === 0 && strlen($qrcode_text) === 27) {
                        $uid = TelegramSessionManager::verify_bind_session(substr($qrcode_text, 11));
                        if ($uid === 0) {
                            $reply['message'] = '绑定失败，二维码无效：' . substr($qrcode_text, 11) . '二维码的有效期为10分钟，请尝试刷新网站的“资料编辑”页面以更新二维码';
                            break;
                        }
                        if (User::where('telegram_id', $message->getFrom()->getId())->count() > 0) {
                            $reply['message'] = '该Telegram已绑定其他账户';
                            break;
                        }
                        $user = User::where('id', $uid)->first();
                        $user->telegram_id = $message->getFrom()->getId();
                        $user->im_type = 4;
                        $user->im_value = $message->getFrom()->getUsername();
                        $user->save();
                        $reply['message'] = '绑定成功，邮箱：' . $user->email;
                        break;
                    }

                    if (strpos($qrcode_text, 'mod://login/') === 0 && strlen($qrcode_text) === 28) {
                        if ($user === null) {
                            $reply['message'] = '登录验证失败，您未绑定本站账号' . substr($qrcode_text, 12);
                            break;
                        }
                        $uid = TelegramSessionManager::verify_login_session(substr($qrcode_text, 12), $user->id);
                        if ($uid !== 0) {
                            $reply['message'] = '登录验证成功，邮箱：' . $user->email;
                        } else {
                            $reply['message'] = '登录验证失败，二维码无效' . substr($qrcode_text, 12);
                        }
                        break;
                    }
            }
        } else {
            //群组
            if ($_ENV['telegram_group_quiet'] === true) {
                return;
            }
            $bot->sendChatAction($message->getChat()->getId(), 'typing');

            switch ($command) {
                case 'ping':
                    $reply['message'] = 'Pong!这个群组的 ID 是 ' . $message->getChat()->getId() . '!';
                    break;
                case 'traffic':
                case 'checkin':
                case 'prpr':
                    $reply = self::needbindMethod($bot, $message, $command, $user, $reply_to);
                    break;
                case 'rss':
                    $reply['message'] = '请私聊机器人使用该命令';
                    break;
                case 'help':
                    $reply['message'] = '命令列表：
						/ping  获取群组ID
						/traffic 查询流量
						/checkin 签到
						/help 获取帮助信息
						/rss 获取节点订阅	';
                    if ($user === null) {
                        $reply['message'] .= PHP_EOL . '您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号';
                    }
                    break;
                default:
                    if ($message->getText() !== null) {
                        if ($message->getChat()->getId() !== $_ENV['telegram_chatid']) {
                            $reply['message'] = '不约，叔叔我们不约';
                        }
                    }
                    if ($message->getNewChatMember() !== null && $_ENV['enable_welcome_message'] === true) {
                        $reply['message'] = '欢迎 ' . $message->getNewChatMember()->getFirstName() . ' ' . $message->getNewChatMember()->getLastName();
                    } else {
                        $reply['message'] = null;
                    }
            }
        }

        $bot->sendMessage($message->getChat()->getId(), $reply['message'], $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to, $replyMarkup = $reply['markup']);
        $bot->sendChatAction($message->getChat()->getId(), '');
    }

    public static function process()
    {
        try {
            $bot = new Client($_ENV['telegram_token']);
            // or initialize with botan.io tracker api key
            // $bot = new \TelegramBot\Api\Client('YOUR_BOT_API_TOKEN', 'YOUR_BOTAN_TRACKER_API_KEY');

            $command_list = ['ping', 'traffic', 'help', 'prpr', 'checkin', 'rss'];
            foreach ($command_list as $command) {
                $bot->command($command, static function ($message) use ($bot, $command) {
                    TelegramProcess::telegramProcess($bot, $message, $command);
                });
            }

            $bot->on($bot->getEvent(static function ($message) use ($bot) {
                TelegramProcess::telegramProcess($bot, $message, '');
            }), static function () {
                return true;
            });

            $bot->on($bot->getCallbackQueryEvent(static function ($callback) use ($bot) {
                TelegramProcess::callbackBindMethod($bot, $callback);
            }), static function () {
                return true;
            });

            $bot->run();
        } catch (Exception $e) {
            $e->getMessage();
        }
    }
}
