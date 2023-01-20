<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\MailBlacklist;

class MailBlockController extends AdminController
{
    public static function page()
    {
        $details = [
            'route' => 'mail/block',
            'title' => [
                'title' => '邮箱黑名单',
                'subtitle' => '浏览和管理针对具体地址的邮箱黑名单',
            ],
            'field' => [
                'id' => '#',
                'addr' => '地址',
                'mark' => '备注',
                'created_at' => '时间',
            ],
            'search_dialog' => [
                [
                    'id' => 'addr',
                    'info' => '地址',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => false, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'mark',
                    'info' => '备注',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => false,
                ],
            ],
            'create_dialog' => [
                [
                    'id' => 'mail_addr',
                    'info' => '地址',
                    'rows' => '10',
                    'type' => 'textarea',
                    'placeholder' => '一行一个',
                ],
                [
                    'id' => 'remark',
                    'info' => '备注',
                    'type' => 'input',
                    'placeholder' => '请输入',
                ],
                [
                    'id' => 'action',
                    'info' => '操作',
                    'type' => 'select',
                    'select' => [
                        'add' => '添加',
                        'remove' => '移除',
                    ],
                ],
            ],
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = MailBlacklist::orderBy('id', 'desc')
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/mail/block.tpl')
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

        $results = MailBlacklist::orderBy('id', 'desc')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    public function ajaxUpdate($request, $response, $args)
    {
        try {
            $action = $request->getParam('action');
            $remark = $request->getParam('remark');
            $mail_addr = $request->getParam('mail_addr');

            if ($remark === '' && $action === 'add') {
                throw new \Exception('请输入备注');
            }

            $count = 0;
            $array = explode("\n", $mail_addr);
            $array_count = count($array);
            $array = array_flip($array);
            $array = array_flip($array);
            $array_unique_count = count($array);

            if ($action === 'add') {
                foreach ($array as $addr) {
                    $record = MailBlacklist::where('addr', $addr)->first();
                    if (!isset($record) && $addr !== '') {
                        $record = new MailBlacklist();
                        $record->addr = $addr;
                        $record->mark = $remark;
                        $record->created_at = time();
                        $record->save();
                        $count++;
                    }
                }
            } else {
                foreach ($array as $addr) {
                    $record = MailBlacklist::where('addr', $addr)->first();
                    if (isset($record)) {
                        $record->delete();
                        $count++;
                    }
                }
            }

            $text = "提交了 {$array_count} 个地址，去重后有 {$array_unique_count} 个地址，有效执行数为 {$count}";
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => $text,
        ]);
    }
}
