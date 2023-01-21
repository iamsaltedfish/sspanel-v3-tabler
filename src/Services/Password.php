<?php

namespace App\Services;

use App\Models\PasswordReset;
use App\Utils\Tools;
use Exception;

class Password
{
    public static function sendResetEmail($email): bool
    {
        $pwdRst = new PasswordReset();
        $pwdRst->email = $email;
        $pwdRst->init_time = time();
        $pwdRst->expire_time = time() + 3600 * 24;
        $pwdRst->token = Tools::genRandomChar(64);
        if (!$pwdRst->save()) {
            return false;
        }
        $subject = $_ENV['appName'] . '重置密码';
        $resetUrl = $_ENV['mail_baseUrl'] . '/password/token/' . $pwdRst->token;
        try {
            Mail::send(
                $email,
                $subject,
                'password/reset.tpl',
                'account_security',
                [
                    'resetUrl' => $resetUrl,
                ],
                []
            );
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
