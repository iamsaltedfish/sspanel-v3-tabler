<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\MailStatistics;

class MailController extends AdminController
{
    public static function page()
    {
        $details = [
            'route' => 'mail',
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

        return $details;
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
            if ($from['type'] == 'input') {
                if ($from['exact']) {
                    ($keyword != '') && array_push($condition, [$field, '=', $keyword]);
                } else {
                    ($keyword != '') && array_push($condition, [$field, 'like', '%' . $keyword . '%']);
                }
            }
            if ($from['type'] == 'select') {
                ($keyword != 'all') && array_push($condition, [$field, '=', $keyword]);
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
}
