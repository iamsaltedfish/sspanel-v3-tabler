<?php

namespace App\Controllers;

use App\Models\MailPush;
use App\Services\View;

class PushController
{
    public function __construct()
    {
        $this->view = View::getSmarty();
    }

    public function view()
    {
        if (View::$connection) {
            $this->view->assign(
                [
                    'queryLog' => View::$connection->connection('default')->getQueryLog(),
                    'optTime' => (microtime(true) - View::$beginTime) * 1000,
                ]
            );
        }
        return $this->view;
    }

    public static function getLists()
    {
        $lists = [
            'basic' => '基础',
            'market' => '营销',
            'due_reminder' => '到期提醒	',
            'account_security' => '账户安全',
            'work_order' => '工单通知',
            'traffic_report' => '流量报告',
            'general_notice' => '一般公告',
            'important_notice' => '重要公告',
        ];

        return $lists;
    }

    public function index($request, $response, $args)
    {
        $access_token = $args['token'];
        $user = MailPush::where('access_token', $access_token)->first();
        if (isset($user)) {
            return $response->write(
                $this->view()
                    ->assign('config', $user)
                    ->assign('token', $access_token)
                    ->assign('lists', self::getLists())
                    ->display('mail/push/index.tpl')
            );
        }
    }

    public function update($request, $response, $args)
    {
        try {
            $lists = self::getLists();
            $access_token = $args['token'];
            $user = MailPush::where('access_token', $access_token)->first();

            if (!isset($user)) {
                throw new \Exception('用户不存在');
            }

            foreach ($lists as $k => $v) {
                $user->$k = $request->getParam($k) === 'true' ? 1 : 0;
            }
            $user->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '已保存您的设置',
        ]);
    }
}
