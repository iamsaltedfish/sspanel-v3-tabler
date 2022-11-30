<?php

namespace App\Services;

use App\Models\MailBlacklist;
use App\Models\MailPush;
use App\Models\MailStatistics;
use App\Models\Setting;
use App\Models\User;
use App\Services\Mail\Mailgun;
use App\Services\Mail\NullMail;
use App\Services\Mail\SendGrid;
use App\Services\Mail\Ses;
use App\Services\Mail\Smtp;
use Smarty;

class Mail
{
    public static function getClient()
    {
        $driver = Setting::obtain('mail_driver');
        switch ($driver) {
            case 'mailgun':
                return new Mailgun();
            case 'ses':
                return new Ses();
            case 'smtp':
                return new Smtp();
            case 'sendgrid':
                return new SendGrid();
            default:
                return new NullMail();
        }
    }

    public static function genHtml($template, $ary)
    {
        $smarty = new smarty();
        $smarty->settemplatedir(BASE_PATH . '/resources/email/');
        $smarty->setcompiledir(BASE_PATH . '/storage/framework/smarty/compile/');
        $smarty->setcachedir(BASE_PATH . '/storage/framework/smarty/cache/');
        // add config
        $smarty->assign('config', Config::getPublicConfig());
        foreach ($ary as $key => $value) {
            $smarty->assign($key, $value);
        }
        return $smarty->fetch($template);
    }

    // $to       收件人
    // $subject  邮件主题
    // $template 模板文件相对位置
    // $type     邮件类型
    // $ary      模板文件内使用的变量
    // $files    文件附件
    public static function send($to, $subject, $template, $type, $ary = [], $files = [])
    {
        $user = User::where('email', $to)->first();
        $record = new MailStatistics();
        $record->user_id = isset($user->id) ? $user->id : 0;
        $record->type = $type;
        $record->addr = $to;
        $record->created_at = time();

        // check user setting
        if (isset($user->id)) {
            $record->status = (MailPush::allow($type, $user->id)) ? 0 : 1; // 0表示正常 1表示用户设置拒收 2表示收信地址在黑名单
        } else {
            $record->status = 0;
        }
        // check block list
        if (MailBlacklist::in($to)) {
            $record->status = 2;
        }
        // save recode
        $record->save();
        // quit
        /* if ($record->status !== 0) {
            return;
        } */

        $text = self::genHtml($template, $ary);
        return self::getClient()->send($to, $subject, $text, $files);
    }
}
