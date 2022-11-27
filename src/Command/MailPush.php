<?php
namespace App\Command;

use App\Models\MailPush as MailPushModel;
use App\Models\User;
use Dariuszp\CliProgressBar;

class MailPush extends Command
{
    public $description = ''
        . '├─=: php xcat MailPush [选项]' . PHP_EOL
        . '│ ├─ generateList            - 为现有用户生成个性化邮件配置' . PHP_EOL
        . '│ ├─ reEncrypt               - 修改盐后执行' . PHP_EOL;

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

    public function generateList()
    {
        $users = User::all(['id', 'email']);
        $salt = $_ENV['mail_push_salt'] ?? 'c669c8b3277fa0415aaea21d78fccdd7'; // default salt

        $bar = new CliProgressBar($users->count());
        $bar->setBarLength(50);
        $bar->display();
        $bar->setColorToYellow();

        foreach ($users as $user) {
            $bar->progress();
            $config = new MailPushModel();
            $config->user_id = $user->id;
            $config->basic = 1;
            $config->market = 1;
            $config->due_reminder = 1;
            $config->account_security = 1;
            $config->work_order = 1;
            $config->traffic_report = 0; // 默认不推送每日流量报告
            $config->general_notice = 1;
            $config->important_notice = 1;
            $config->access_token = md5($user->id . $user->email . $salt);
            $config->created_at = time();
            $config->updated_at = time();
            $config->save();
        }

        $bar->setColorToGreen();
        $bar->display();
        $bar->end();

        echo "All user generated customize mail receiving configuration is complete.\n";
    }

    public function reEncrypt()
    {
        $users = User::all(['id', 'email']);
        $salt = $_ENV['mail_push_salt'] ?? 'c669c8b3277fa0415aaea21d78fccdd7'; // default salt

        foreach ($users as $user) {
            $config = MailPushModel::where('user_id', $user->id)->first();
            $config->access_token = md5($user->id . $user->email . $salt);
            $config->save();
        }

        echo "All user access tokens have been reset.\n";
    }
}
