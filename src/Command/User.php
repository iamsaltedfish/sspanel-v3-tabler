<?php

namespace App\Command;

use App\Controllers\AuthController;
use App\Models\User as ModelsUser;
use App\Services\Mail;
use Exception;

class User extends Command
{
    public $description = ''
        . '├─=: php xcat User [选项]' . PHP_EOL
        . '│ ├─ createAdmin             - 创建管理员帐号' . PHP_EOL
        . '│ ├─ sendAdminMail           - 向管理员发送通知邮件' . PHP_EOL
        . '│ ├─ sendUserMail            - 向用户发送通知邮件' . PHP_EOL
        . '│ ├─ resetUserLevel          - 重置用户等级' . PHP_EOL;

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

    public function sendAdminMail()
    {
        $admin_user = ModelsUser::where('is_admin', 1)->get();
        foreach ($admin_user as $user) {
            echo "send email to {$user->id}...\n";
            Mail::send(
                $user->email,
                $_ENV['appName'],
                'notice.tpl',
                'important_notice',
                [
                    'title' => 'title',
                    'content' => 'content',
                    'concluding_remarks' => '此邮件由系统发送，回复邮件的内容无法被接收。',
                ],
                []
            );
        }
    }

    public function sendUserMail()
    {
        $normal_user = ModelsUser::all();
        foreach ($normal_user as $user) {
            echo "send email to {$user->id}...\n";
            try {
                Mail::send(
                    $user->email,
                    $_ENV['appName'],
                    'notice.tpl',
                    'important_notice',
                    [
                        'title' => 'title',
                        'content' => 'content',
                        'concluding_remarks' => '此邮件由系统发送，回复邮件的内容无法被接收。',
                    ],
                    []
                );
            } catch (\Exception $e) {
                echo "fail to send {$user->id}...\n";
            }
        }
    }

    public function resetUserLevel()
    {
        $users = ModelsUser::all();
        foreach ($users as $user) {
            $user->class = 0;
            $user->class_expire = $user->expire_in;
            $user->save();
        }
        echo 'All user levels have been reset to 0, and the level expiration time is sync with the account expiration time.' . PHP_EOL;
    }

    public function createAdmin()
    {
        if (count($this->argv) === 3) {
            // ask for input
            fwrite(STDOUT, '(1/3) 请输入管理员邮箱：') . PHP_EOL;
            // get input
            $email = trim(fgets(STDIN));
            if ($email === null) {
                die("必须输入管理员邮箱.\r\n");
            }

            // write input back
            fwrite(STDOUT, "(2/3) 请输入管理员账户密码：") . PHP_EOL;
            $passwd = trim(fgets(STDIN));
            if ($passwd === null) {
                die("必须输入管理员密码.\r\n");
            }

            fwrite(STDOUT, "(3/3) 按 Y 或 y 确认创建：");
            $y = trim(fgets(STDIN));
        } elseif (count($this->argv) === 5) {
            [, , , $email, $passwd] = $this->argv;
            $y = 'y';
        }

        if (strtolower($y) === 'y') {
            try {
                AuthController::register_helper('admin', $email, $passwd, '', '1', '', 0, false, 'null');
                $last_user = ModelsUser::where('email', $email)->first();
                $last_user->is_admin = 1;
                $last_user->save();
            } catch (\Exception $e) {
                $error_msg = $e->getMessage();
            }

            if (isset($error_msg)) {
                echo PHP_EOL . '创建失败，以下是错误信息：' . PHP_EOL;
                die($error_msg);
            }

            echo PHP_EOL . '创建成功，请在主页登录' . PHP_EOL;
        } else {
            echo PHP_EOL . '已取消创建' . PHP_EOL;
        }
    }
}
