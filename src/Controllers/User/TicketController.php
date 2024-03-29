<?php

namespace App\Controllers\User;

use App\Controllers\TelegramController;
use App\Controllers\UserController;
use App\Models\User;
use App\Models\WorkOrder;
use voku\helper\AntiXSS;

class TicketController extends UserController
{
    public function ticket($request, $response, $args)
    {
        if ($_ENV['enable_ticket'] === false) {
            return null;
        }

        $tickets = WorkOrder::where('user_id', $this->user->id)
            ->where('is_topic', 1)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return $response->write(
            $this->view()
                ->assign('tickets', $tickets)
                ->display('user/ticket/index.tpl')
        );
    }

    public function ticketCreate($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->display('user/ticket/create.tpl')
        );
    }

    public function ticketAdd($request, $response, $args)
    {
        $anti_xss = new AntiXSS();
        $title = $request->getParam('title');
        $content = $request->getParam('content');
        $ticket_client = (string) $request->getParam('ticket_client');

        try {
            if ($title === '') {
                throw new \Exception('请填写工单标题');
            }
            if (strlen($title) > 20) {
                throw new \Exception('工单标题长度应控制在20个字符内');
            }
            if ($content === '' && $ticket_client !== 'reward_or_refund') {
                throw new \Exception('请填写工单内容');
            }
            if (strlen($content) > 500) {
                throw new \Exception('工单内容长度不得超过500字符');
            }
            if ($ticket_client === '0') {
                throw new \Exception('请选择工单类型，不要使用第一个选项');
            }
            if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
                throw new \Exception('工单内容不能包含关键词 admin 和 user');
            }
            if ($ticket_client === 'reward_or_refund') {
                $ticket_client = '提现或退款';
                $content = '收款方式：' . $request->getParam('receiving_method') . PHP_EOL;
                $content .= '收款账户：' . $request->getParam('receiving_account') . PHP_EOL;
                $content .= '备注信息：' . $request->getParam('associated_order') . PHP_EOL;
            }
            // 拼接内容
            $ticket_content = '【我的问题】' . PHP_EOL . $ticket_client . PHP_EOL . PHP_EOL;
            $ticket_content .= '【问题详情】' . PHP_EOL . $content . PHP_EOL;
            // xss过滤
            $title = $anti_xss->xss_clean($title);
            $ticket_content = $anti_xss->xss_clean($ticket_content);
            // 保存
            $last_tk_id = WorkOrder::where('is_topic', 1)->orderBy('id', 'desc')->first();

            $ticket = new WorkOrder();
            $ticket->tk_id = $last_tk_id === null ? 1 : $last_tk_id->tk_id + 1;
            $ticket->is_topic = 1;
            $ticket->title = $title;
            $ticket->content = $ticket_content;
            $ticket->user_id = $this->user->id;
            $ticket->wait_reply = 'admin';
            $ticket->created_at = time();
            $ticket->updated_at = time();
            $ticket->closed_at = null;
            $ticket->closed_by = null;
            $ticket->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        if ($_ENV['admin_ticket_mail_notify']) {
            $admins = User::where('is_admin', 1)->get();
            foreach ($admins as $admin) {
                $admin->sendMail(
                    $_ENV['appName'] . ' - 新的工单',
                    'news/warn.tpl',
                    'work_order',
                    [
                        'text' => '新工单：' . $title . '<br />'
                        . nl2br($ticket_content),
                    ],
                    [],
                    true
                );
            }
        }
        if ($_ENV['admin_ticket_telegram_notify']) {
            $send_text = sprintf("#%d\n", $ticket->tk_id);
            $send_text .= sprintf("工单主题：\n" . "`%s`\n\n", $ticket->title);
            $send_text .= sprintf("工单内容：\n" . "`%s`", $ticket->content);
            TelegramController::sendMessageToAdmin($send_text);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '新工单已创建',
        ]);
    }

    public function ticketUpdate($request, $response, $args)
    {
        try {
            $tk_id = $args['id'];
            $ticket = WorkOrder::where('tk_id', $tk_id)->first();
            if ($ticket === null) {
                throw new \Exception('回复的主题帖不存在');
            }
            $topic = WorkOrder::where('tk_id', $tk_id)
                ->where('is_topic', '1')
                ->first();
            if ($topic->user_id !== $this->user->id) {
                throw new \Exception('此主题帖不属于你');
            }
            if ($topic->getOriginal('closed_by') !== null) {
                $close_role = ['admin', 'user', 'system'];
                if (in_array($topic->getOriginal('closed_by'), $close_role, true)) {
                    throw new \Exception('此主题帖已关闭，如有需要请创建新工单');
                }
            }
            $anti_xss = new AntiXSS();
            $raw_content = $request->getParam('content');
            $content = $anti_xss->xss_clean($raw_content);
            if ($content === '') {
                throw new \Exception('请撰写回复内容');
            }
            if (strlen($content) > 500) {
                throw new \Exception('工单内容长度不得超过500字符');
            }
            if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
                throw new \Exception('回复内容不能包含关键词 admin 和 user');
            }

            $ticket = new WorkOrder();
            $ticket->tk_id = $tk_id;
            $ticket->is_topic = 0;
            $ticket->title = null;
            $ticket->content = $content;
            $ticket->user_id = $this->user->id;
            $ticket->created_at = time();
            $ticket->updated_at = time();
            $ticket->closed_at = null;
            $ticket->closed_by = null;
            $ticket->save();

            $topic->updated_at = time();
            $topic->wait_reply = 'admin';
            $topic->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        if ($_ENV['admin_ticket_mail_notify']) {
            $admins = User::where('is_admin', 1)->get();
            foreach ($admins as $admin) {
                $admin->sendMail(
                    $_ENV['appName'] . ' - 用户工单回复',
                    'news/warn.tpl',
                    'work_order',
                    [
                        'text' => '工单编号：#' . $tk_id .
                        '<br/>工单主题：' . $topic->title .
                        '<br/>回复内容：' . $content,
                    ],
                    [],
                    true
                );
            }
        }
        if ($_ENV['admin_ticket_telegram_notify']) {
            $send_text = sprintf("#%d\n", $tk_id);
            $send_text .= sprintf("工单主题：\n" . "`%s`\n\n", $topic->title);
            $send_text .= sprintf("回复内容：\n" . "`%s`", $content);
            TelegramController::sendMessageToAdmin($send_text);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '回复成功',
        ]);
    }

    public function ticketView($request, $response, $args)
    {
        $tk_id = $args['id'];
        $topic = WorkOrder::where('tk_id', $tk_id)
            ->where('is_topic', '1')
            ->first();

        if ($topic === null || $topic->user_id !== $this->user->id) {
            // 避免平级越权
            return null;
        }

        $discussions = WorkOrder::where('tk_id', $tk_id)->get();

        return $response->write(
            $this->view()
                ->assign('topic', $topic)
                ->assign('discussions', $discussions)
                ->display('user/ticket/read.tpl')
        );
    }
}
