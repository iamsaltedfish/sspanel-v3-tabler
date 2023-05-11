<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Fingerprint;
use App\Models\Payback;
use App\Models\User;

class PaybackController extends AdminController
{
    public static function page()
    {
        return [
            'route' => 'payback',
            'title' => [
                'title' => '返利记录',
                'subtitle' => '邀请注册的用户返利给邀请人的记录',
            ],
            'field' => [
                'id' => '#',
                'total' => '订单金额',
                'userid' => '订单用户',
                'ref_by' => '邀请人',
                'ref_get' => '返利金额',
                'fraud_detect' => '是否存疑',
                'associated_order' => '关联订单',
                'datetime' => '返利时间',
            ],
            'search_dialog' => [
                [
                    'id' => 'userid',
                    'info' => '订单用户',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'ref_by',
                    'info' => '邀请人',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'ref_get',
                    'info' => '返利金额',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => false,
                ],
                [
                    'id' => 'associated_order',
                    'info' => '关联订单',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'fraud_detect',
                    'info' => '是否存疑',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有状态',
                        '0' => '通过',
                        '1' => '存疑',
                    ],
                    'exact' => true,
                ],
            ],
        ];
    }

    public function index($request, $response, $args)
    {
        $condition = [];
        $user_id = $request->getParam('user_id');
        if (isset($user_id)) {
            $condition[] = ['ref_by', '=', $user_id];
        }
        $logs = Payback::where($condition)
            ->orderBy('id', 'desc')
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/payback.tpl')
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

        $results = Payback::orderBy('id', 'desc')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    public function delete($request, $response, $args)
    {
        $item_id = $args['id'];
        Payback::find($item_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    public function amendmentReward($request, $response, $args)
    {
        $item_id = $args['id'];
        $reward = Payback::find($item_id);
        $invite_sponsor = $reward->ref_by; // 邀请发起人（受益方）

        if ($reward->getOriginal('fraud_detect') === 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '此功能仅适用于认定为存疑的返利记录',
            ]);
        }

        $invite_sponsor_user = User::find($invite_sponsor);
        if ($invite_sponsor_user !== null) {
            $invite_sponsor_user->money += $reward->ref_get;
            $invite_sponsor_user->save();
        }
        $reward->fraud_detect = 0;
        $reward->save();

        Fingerprint::where('user_id', $reward->userid)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '操作成功',
        ]);
    }
}
