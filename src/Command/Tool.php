<?php

namespace App\Command;

use App\Models\LoginIp;
use App\Models\Node;
use App\Models\Setting;
use App\Models\User;
use App\Utils\QQWry;
use App\Utils\Tools;

class Tool extends Command
{
    public $description = ''
        . '├─=: php xcat Tool [选项]' . PHP_EOL
        . '│ ├─ initQQWry                     - 下载 IP 解析库' . PHP_EOL
        . '│ ├─ setTelegram                   - 设置 Telegram 机器人' . PHP_EOL
        . '│ ├─ resetAllSettings              - 使用默认值覆盖设置中心设置' . PHP_EOL
        . '│ ├─ exportAllSettings             - 导出所有设置' . PHP_EOL
        . '│ ├─ importAllSettings             - 导入所有设置' . PHP_EOL
        . '│ ├─ mailboxSuffixCount            - 统计注册用户邮箱域' . PHP_EOL
        . '│ ├─ supplementaryLoginAttribution - 补充登录日志归属' . PHP_EOL
        . '│ ├─ completeNickname              - 为空字符串昵称用户补全昵称' . PHP_EOL
        . '│ ├─ conversionTransferConfig      - 转换中转配置' . PHP_EOL;

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

    public function setTelegram()
    {
        if ($_ENV['telegram_bot'] === '' || $_ENV['telegram_token'] === '' || $_ENV['enable_telegram'] === false) {
            echo "Please try again after configuring telegram_bot, telegram_token, enable_telegram items in config/.config.php file" . PHP_EOL;
            return;
        }

        try {
            $web_hook_url = $_ENV['baseUrl'] . '/telegramCallback?token=' . md5($_ENV['muKey']);
            $telegram = new \Telegram\Bot\Api($_ENV['telegram_token']);
            $telegram->removeWebhook();
            if ($telegram->setWebhook(['url' => $web_hook_url])) {
                $bot_name = $telegram->getMe()->getUsername();
                echo "The new version telegram robot @{$bot_name} has been set up." . PHP_EOL;
                $rep = file_get_contents(sprintf("https://api.telegram.org/bot%s/getWebhookInfo", $_ENV['telegram_token']));
                echo json_encode(json_decode($rep, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
            }
        } catch (\Exception $e) {
            echo "Unable to set webhooks: {$e->getMessage()}" . PHP_EOL;
        }
    }

    public function initQQWry()
    {
        echo 'Files are being pulled from source...' . PHP_EOL;

        $path = BASE_PATH . '/storage/qqwry.dat';
        $qqwry = file_get_contents('https://github.com/out0fmemory/qqwry.dat/raw/master/historys/2022_04_20/qqwry.dat');
        if ($qqwry !== '') {
            if (is_file($path)) {
                rename($path, $path . '.bak');
            }
            $fp = fopen($path, 'wb');
            if ($fp) {
                fwrite($fp, $qqwry);
                fclose($fp);
                $iplocation = new QQWry();
                $location = $iplocation->getlocation('8.8.8.8');
                if (iconv('gbk', 'utf-8//IGNORE', $location['country']) !== '美国') {
                    unlink($path);
                    if (is_file($path . '.bak')) {
                        rename($path . '.bak', $path);
                    }
                }
                echo 'The local files have been synchronized with the source.' . PHP_EOL;
            } else {
                echo 'Unable to save file, please check write permissions.' . PHP_EOL;
            }
        } else {
            echo 'Invalid download source or network error.' . PHP_EOL;
        }
    }

    public function resetAllSettings()
    {
        $settings = Setting::all();

        foreach ($settings as $setting) {
            $setting->value = $setting->default;
            $setting->save();
        }

        echo '已使用默认值覆盖所有设置.' . PHP_EOL;
    }

    public function exportAllSettings()
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {
            // 因为主键自增所以即便设置为 null 也会在导入时自动分配 id
            // 同时避免多位开发者 pull request 时 settings.json 文件 id 重复所可能导致的冲突
            $setting->id = null;
            // 避免开发者调试配置泄露
            $setting->value = $setting->default;
        }

        $json_settings = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents('./config/settings.json', $json_settings);

        echo '已导出所有设置.' . PHP_EOL;
    }

    public function importAllSettings()
    {
        $json_settings = file_get_contents('./config/settings.json');
        $settings = json_decode($json_settings, true);
        $number = count($settings);
        $counter = 0;

        for ($i = 0; $i < $number; $i++) {
            $item = $settings[$i]['item'];
            $object = Setting::where('item', $item)->first();

            if ($object === null) {
                $new_item = new Setting();
                $new_item->id = null;
                $new_item->item = $settings[$i]['item'];
                $new_item->value = $settings[$i]['value'];
                $new_item->class = $settings[$i]['class'];
                $new_item->is_public = $settings[$i]['is_public'];
                $new_item->type = $settings[$i]['type'];
                $new_item->default = $settings[$i]['default'];
                $new_item->mark = $settings[$i]['mark'];
                $new_item->save();

                echo "添加新设置：${item}" . PHP_EOL;
                $counter += 1;
            }
        }

        if ($counter !== 0) {
            echo "总计添加了 ${counter} 条新设置." . PHP_EOL;
        } else {
            echo "没有任何新设置需要添加." . PHP_EOL;
        }
    }

    public function supplementaryLoginAttribution()
    {
        LoginIp::chunkById(1000, static function ($logs) {
            foreach ($logs as $log) {
                if (!isset($log->attribution)) {
                    $log->attribution = Tools::getIpInfo($log->ip);
                    $log->save();
                }
            }
        });
    }

    public function mailboxSuffixCount()
    {
        $set = [];
        $users = User::all(['email']);
        foreach ($users as $user) {
            $mail = explode('@', $user->email);
            if (!isset($set[$mail[1]])) {
                $set[$mail[1]] = 1;
            } else {
                $set[$mail[1]] += 1;
            }
        }
        arsort($set);
        echo json_encode($set, JSON_PRETTY_PRINT) . PHP_EOL;
    }

    public function completeNickname()
    {
        $users = User::where('user_name', '')
            ->get(['id', 'email', 'user_name']);

        foreach ($users as $user) {
            $split = explode('@', $user->email);
            //print("{$user->id} -> {$split[0]}") . PHP_EOL;
            $user->user_name = $split[0];
            $user->save();
        }

        echo "All tasks have been completed." . PHP_EOL;
    }

    public static function parsingAdditionalParameters(string $text): array
    {
        $result = [];
        $str = explode('|', $text);
        foreach ($str as $key) {
            $content = explode('=', $key);
            $result[$content[0]] = $content[1];
        }

        return $result;
    }

    public function conversionTransferConfig()
    {
        $nodes = Node::where('server', 'like', '%relayserver%')
            ->where('type', 1)
            ->where('sort', 11)
            ->get();

        foreach ($nodes as $node) {
            $split = explode(';', $node->server);
            $params = self::parsingAdditionalParameters($split[5]);
            $array = [
                [
                    'display_name' => $node->name,
                    'connect_addr' => $params['relayserver'],
                    'connect_port' => $params['outside_port'],
                    'sni_instruction' => $split[0],
                ],
            ];
            $node->add_in = 0;
            $node->transit_enable = 1;
            $node->transit_json = json_encode($array, 320);
            $node->save();
        }

        echo "Total {$nodes->count()} has been process." . PHP_EOL;
    }
}
