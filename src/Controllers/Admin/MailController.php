<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\EmailQueue;
use App\Models\EmailTask;
use App\Models\MailPush;
use App\Models\MailStatistics;
use App\Models\User;
use App\Utils\Tools;

class MailController extends AdminController
{
    public static function page()
    {
        return [
            'route' => 'mail/log',
            'title' => [
                'title' => '邮件日志',
                'subtitle' => '系统发送的邮件日志',
            ],
            'field' => [
                'id' => '#',
                'user_id' => '请求用户',
                'type' => '邮件类型',
                'addr' => '收件地址',
                'status' => '发送状态',
                'created_at' => '创建时间',
            ],
            'search_dialog' => [
                [
                    'id' => 'user_id',
                    'info' => '请求用户',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'type',
                    'info' => '邮件类型',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'addr',
                    'info' => '收件地址',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'status',
                    'info' => '发送状态',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有',
                        '0' => '正常',
                        '1' => '用户拒收',
                        '2' => '黑名单中',
                    ],
                    'exact' => true,
                ],
            ],
        ];
    }

    public function index($request, $response, $args)
    {
        $logs = MailStatistics::orderBy('id', 'desc')
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/mail/log.tpl')
        );
    }

    public function ajaxQuery($request, $response, $args)
    {
        $condition = [];
        $details = self::page();
        foreach ($details['search_dialog'] as $from) {
            $field = $from['id'];
            $keyword = $request->getParam($field);
            if ($from['type'] === 'input') {
                if ($from['exact']) {
                    ($keyword !== '') && array_push($condition, [$field, '=', $keyword]);
                } else {
                    ($keyword !== '') && array_push($condition, [$field, 'like', '%' . $keyword . '%']);
                }
            }
            if ($from['type'] === 'select') {
                ($keyword !== 'all') && array_push($condition, [$field, '=', $keyword]);
            }
        }

        $results = MailStatistics::orderBy('id', 'desc')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    public static function getDefaultGroup($x, $y): array
    {
        return [
            'all_users' => [
                'display_name' => '全体用户',
                'condition' => [
                    ['enable', '=', 1],
                ],
            ],
            'administrators_only' => [
                'display_name' => '仅限管理员',
                'condition' => [
                    ['is_admin', '=', 1],
                ],
            ],
            'non_administrator_only' => [
                'display_name' => '仅限非管理员',
                'condition' => [
                    ['is_admin', '!=', 1],
                ],
            ],
            'remark_eq_x' => [
                'display_name' => '限定备注为 x',
                'condition' => [
                    ['remark', '=', $x],
                ],
            ],
            'user_id_ge_x' => [
                'display_name' => '用户id大于 x',
                'condition' => [
                    ['id', '>', $x],
                ],
            ],
            'user_id_le_x' => [
                'display_name' => '用户id小于 x',
                'condition' => [
                    ['id', '<', $x],
                ],
            ],
            'user_id_between_x_y' => [
                'display_name' => '用户id在区间 [x,y]',
                'condition' => [
                    ['id', '>=', $x],
                    ['id', '<=', $y],
                ],
            ],
            'user_level_x' => [
                'display_name' => '等级为 x',
                'condition' => [
                    ['class', '=', $x],
                ],
            ],
            'user_level_ge_x' => [
                'display_name' => '等级大于等于 x',
                'condition' => [
                    ['class', '>=', $x],
                ],
            ],
            'user_level_le_x' => [
                'display_name' => '等级小于等于 x',
                'condition' => [
                    ['class', '<=', $x],
                ],
            ],
            'node_group_x' => [
                'display_name' => '节点群组为 x',
                'condition' => [
                    ['node_group', '=', $x],
                ],
            ],
            'reg_date_before_x' => [
                'display_name' => '注册时间在 x 之前',
                'condition' => [
                    ['reg_date', '<=', $x],
                ],
            ],
            'reg_date_after_x' => [
                'display_name' => '注册时间在 x 之后',
                'condition' => [
                    ['reg_date', '>=', $x],
                ],
            ],
            'reg_date_between_x_y' => [
                'display_name' => '注册时间在 [x, y] 之间',
                'condition' => [
                    ['reg_date', '>=', $x],
                    ['reg_date', '<=', $y],
                ],
            ],
            'expire_in_before_x' => [
                'display_name' => '账户过期时间在 x 之前',
                'condition' => [
                    ['expire_in', '<=', $x],
                ],
            ],
            'expire_in_after_x' => [
                'display_name' => '账户过期时间在 x 之后',
                'condition' => [
                    ['expire_in', '>=', $x],
                ],
            ],
            'expire_in_between_x_y' => [
                'display_name' => '账户过期时间在 [x, y] 之间',
                'condition' => [
                    ['expire_in', '>=', $x],
                    ['expire_in', '<=', $y],
                ],
            ],
            'class_expire_before_x' => [
                'display_name' => '等级过期时间在 x 之前',
                'condition' => [
                    ['class_expire', '<=', $x],
                ],
            ],
            'class_expire_after_x' => [
                'display_name' => '等级过期时间在 x 之后',
                'condition' => [
                    ['class_expire', '>=', $x],
                ],
            ],
            'class_expire_between_x_y' => [
                'display_name' => '等级过期时间在 [x, y] 之间',
                'condition' => [
                    ['class_expire', '>=', $x],
                    ['class_expire', '<=', $y],
                ],
            ],
            'money_ge_x' => [
                'display_name' => '账户余额大于等于 x',
                'condition' => [
                    ['money', '>=', $x],
                ],
            ],
            'money_le_x' => [
                'display_name' => '账户余额小于等于 x',
                'condition' => [
                    ['money', '<=', $x],
                ],
            ],
            'mailbox_domain_x' => [
                'display_name' => '邮箱域为 x (如qq.com)',
                'condition' => [
                    ['email', 'like', '%' . '@' . $x . '%'],
                ],
            ],
        ];
    }

    public function task($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->assign('default_group', self::getDefaultGroup(1, 1))
                ->assign('task_coding', strtoupper(Tools::genRandomChar(10)))
                ->display('admin/mail/createTask.tpl')
        );
    }

    public function preview($request, $response, $args)
    {
        $title = $request->getParam('title');
        $content = $request->getParam('content');
        $mail_category = $request->getParam('mail_category');
        $unsub_link = $_ENV['mail_baseUrl'] . '/mail/push/' . $this->user->getMailUnsubToken();
        $concluding_remarks = "此邮件由系统自动发送，分类是 <b>${mail_category}</b>。取消此类通知，请前往 <a href=\"{$unsub_link}\">邮件推送</a> 页面设置";

        return $response->write(
            $this->view()
                ->assign('title', $title)
                ->assign('content', $content)
                ->assign('concluding_remarks', $concluding_remarks)
                ->display('admin/mail/preview.tpl')
        );
    }

    public static function checkValidTime(string $datetime): bool
    {
        $timestamp = strtotime($datetime);
        $to_datetime = date('Y-m-d H:i:s', $timestamp);
        return ($datetime != $to_datetime) ? false : true;
    }

    public function filter($request, $response, $args)
    {
        $variable_x = $request->getParam('variable_x'); // int or datetime
        $variable_y = $request->getParam('variable_y'); // int or datetime
        $receiving_group = $request->getParam('receiving_group'); // text (array index)
        $custom_filtering = $request->getParam('custom_filtering'); // (string) true / false

        try {
            if ($custom_filtering === 'true') {
                $customize_filtering_conditions = $request->getParam('customize_filtering_conditions');
                $condition = json_decode($customize_filtering_conditions, true);
                $decode_error = json_last_error();
                if ($decode_error !== 0) {
                    throw new \Exception('未能正确解析 JSON 自定义筛选配置');
                }
            } else {
                $default_filter = self::getDefaultGroup($variable_x, $variable_y);
                if (!isset($default_filter[$receiving_group])) {
                    throw new \Exception('不存在的条件');
                }
                $display_name = $default_filter[$receiving_group]['display_name'];
                if (strpos($display_name, 'x') && $variable_x === '') {
                    throw new \Exception('没有设置变量 x 的值');
                }
                if (strpos($display_name, 'y') && $variable_y === '') {
                    throw new \Exception('没有设置变量 y 的值');
                }
                if (strpos($display_name, '时间') && strpos($display_name, 'x') && !self::checkValidTime($variable_x)) {
                    throw new \Exception('未能正确解析变量 x 的时间');
                }
                if (strpos($display_name, '时间') && strpos($display_name, 'y') && !self::checkValidTime($variable_y)) {
                    throw new \Exception('未能正确解析变量 y 的时间');
                }
                $condition = $default_filter[$receiving_group]['condition'];
            }
            $count = User::where($condition)->count();
            return $response->withJson([
                'ret' => 1,
                'msg' => "共有 ${count} 名用户将收到消息",
            ]);
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function progress($request, $response, $args)
    {
        $variable_x = $request->getParam('variable_x'); // int or datetime
        $variable_y = $request->getParam('variable_y'); // int or datetime
        $task_coding = $request->getParam('task_coding'); // random string
        $push_title = $request->getParam('push_title');
        $push_content = $request->getParam('push_content');
        $mail_category = $request->getParam('mail_category');
        $mail_category_text = $request->getParam('mail_category_text');
        $receiving_group = $request->getParam('receiving_group'); // text (array index)
        $custom_filtering = $request->getParam('custom_filtering'); // (string) true / false

        try {
            if ($push_title === '') {
                throw new \Exception('请输入推送标题');
            }
            if ($push_content === '') {
                throw new \Exception('请输入推送正文');
            }
            if ($custom_filtering === 'true') {
                $customize_filtering_conditions = $request->getParam('customize_filtering_conditions');
                $condition = json_decode($customize_filtering_conditions, true);
                $decode_error = json_last_error();
                if ($decode_error !== 0) {
                    throw new \Exception('未能正确解析 JSON 自定义筛选配置');
                }
            } else {
                $default_filter = self::getDefaultGroup($variable_x, $variable_y);
                if (!isset($default_filter[$receiving_group])) {
                    throw new \Exception('不存在的条件');
                }
                $display_name = $default_filter[$receiving_group]['display_name'];
                if (strpos($display_name, 'x') && $variable_x === '') {
                    throw new \Exception('没有设置变量 x 的值');
                }
                if (strpos($display_name, 'y') && $variable_y === '') {
                    throw new \Exception('没有设置变量 y 的值');
                }
                if (strpos($display_name, '时间') && strpos($display_name, 'x') && !self::checkValidTime($variable_x)) {
                    throw new \Exception('未能正确解析变量 x 的时间');
                }
                if (strpos($display_name, '时间') && strpos($display_name, 'y') && !self::checkValidTime($variable_y)) {
                    throw new \Exception('未能正确解析变量 y 的时间');
                }
                $condition = $default_filter[$receiving_group]['condition'];
            }
            // 创建任务
            $users = User::where($condition)->get();
            $insert_queue = [];
            foreach ($users as $user) {
                if (MailPush::allow($mail_category, $user->id)) {
                    $unsub_link = $_ENV['mail_baseUrl'] . '/mail/push/' . $user->getMailUnsubToken();
                    $concluding_remarks = "此邮件由系统自动发送，分类是 <b>${mail_category_text}</b>。取消此类通知，请前往 <a href=\"{$unsub_link}\">邮件推送</a> 页面设置";
                    // insert
                    $insert_queue[] = [
                        'to_email' => $user->email,
                        'task_coding' => $task_coding,
                        'subject' => $push_title,
                        'template' => 'notice.tpl',
                        'mail_type' => $mail_category,
                        'array' => json_encode([
                            'title' => $push_title,
                            'content' => $push_content,
                            'concluding_remarks' => $concluding_remarks,
                        ]),
                        'time' => time(),
                    ];
                }
            }
            EmailQueue::insert($insert_queue);
            // 记录任务
            $record = new EmailTask();
            $record->task_coding = $task_coding;
            $record->push_title = $push_title;
            $record->push_content = $push_content;
            $record->params = json_encode([
                'variable_x' => $variable_x,
                'variable_y' => $variable_y,
                'mail_category' => $mail_category,
                'mail_category_text' => $mail_category_text,
                'receiving_group' => $receiving_group,
                'custom_filtering' => $custom_filtering,
            ]);
            $record->recipients_count = $users->count();
            $record->created_at = time();
            $record->save();
            // 输出
            return $response->withJson([
                'ret' => 1,
                'msg' => "任务添加成功",
            ]);
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function progressList($request, $response, $args)
    {
        $tasks = EmailTask::orderBy('id', 'desc')
            ->limit(6)
            ->get();

        return $response->write(
            $this->view()
                ->assign('tasks', $tasks)
                ->registerClass('EmailQueue', EmailQueue::class)
                ->display('admin/mail/tasks.tpl')
        );
    }
}
