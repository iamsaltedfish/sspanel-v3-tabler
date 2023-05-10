<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\Mail;
use App\Utils\Hash;
use App\Utils\Tools;

class UserController extends AdminController
{
    public static function page()
    {
        return [
            'route' => 'user',
            'title' => [
                'title' => '用户列表',
                'subtitle' => '系统中部分注册用户，表格仅展示 ' . $_ENV['page_load_data_entry'] . ' 条',
            ],
            'field' => [
                'id' => '#',
                'user_name' => '昵称',
                'email' => '邮箱',
                'money' => '余额',
                'ref_by' => '邀请人',
                'transfer_enable' => '流量限制',
                'last_day_t' => '累计用量',
                'class' => '等级',
                'invite_num' => '邀请数',
                'reg_date' => '注册时间',
                'expire_in' => '账户过期',
                'class_expire' => '等级过期',
            ],
            'search_dialog' => [
                [
                    'id' => 'id',
                    'info' => '编号',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'user_name',
                    'info' => '昵称',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'email',
                    'info' => '邮箱',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'ref_by',
                    'info' => '邀请人',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'class',
                    'info' => '等级',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'enable',
                    'info' => '状态',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有状态',
                        '0' => '禁用',
                        '1' => '启用',
                    ],
                    'exact' => true,
                ],
            ],
            'create_dialog' => [
                [
                    'id' => 'email',
                    'info' => '登录邮箱',
                    'type' => 'input',
                    'placeholder' => '',
                ],
                [
                    'id' => 'password',
                    'info' => '登录密码',
                    'type' => 'input',
                    'placeholder' => '留空则随机生成',
                ],
                [
                    'id' => 'ref_by',
                    'info' => '邀请人',
                    'type' => 'input',
                    'placeholder' => '邀请人的注册邮箱或用户id，可留空',
                ],
                [
                    'id' => 'email_notify',
                    'info' => '登录凭证',
                    'type' => 'select',
                    'select' => [
                        '1' => '发送登录凭证至新用户邮箱',
                        '0' => '不发送',
                    ],
                ],
            ],
            'update_field' => [
                'email',
                'user_name',
                'remark',
                //'reset_user_passwd',
                'money',
                'is_multi_user',
                //'is_admin',
                //'enable',
                //'ga_enable',
                'transfer_enable',
                'invite_num',
                'ref_by',
                'class_expire',
                'expire_in',
                'node_group',
                'class',
                'node_speedlimit',
                'node_connector',
                //ss
                'port',
                'passwd',
                'method',
                'protocol',
                'protocol_param',
                'obfs',
                'obfs_param',
            ],
        ];
    }

    public function index($request, $response, $args)
    {
        $logs = User::orderBy('id', 'desc')
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        foreach ($logs as $log) {
            $log->transfer_enable = round($log->transfer_enable / 1073741824, 2);
            $log->last_day_t = round($log->last_day_t / 1073741824, 2);
            $log->invite_num = User::where('ref_by', $log->id)->count();
        }

        $products = Product::where('type', '!=', 'other')->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->assign('products', $products)
                ->display('admin/user/index.tpl')
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

        $results = User::orderBy('id', 'desc')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        foreach ($results as $result) {
            $result->transfer_enable = round($result->transfer_enable / 1073741824, 2);
            $result->last_day_t = round($result->last_day_t / 1073741824, 2);
            $result->invite_num = User::where('ref_by', $result->id)->count();
        }

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    public function delete($request, $response, $args)
    {
        $item_id = $args['id'];
        $user = User::find($item_id);

        if (!$user->killUser()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    public function edit($request, $response, $args)
    {
        $user = User::find($args['id']);
        return $response->write(
            $this->view()
                ->assign('field', self::page()['update_field'])
                ->assign('edit_user', $user)
                ->display('admin/user/edit.tpl')
        );
    }

    public static function checkValidTime(string $datetime): bool
    {
        $timestamp = strtotime($datetime);
        $to_datetime = date('Y-m-d H:i:s', $timestamp);
        return ($datetime != $to_datetime) ? false : true;
    }

    public function update($request, $response, $args)
    {
        try {
            $id = $args['id'];
            $user = User::find($id);
            $field = self::page()['update_field'];
            foreach ($field as $key) {
                $user->$key = $request->getParam($key);
            }
            // 特殊处理一些字段
            $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
            $user->is_admin = ($request->getParam('is_admin') === 'true') ? 1 : 0; // 值为1时是管理员
            $user->enable = ($request->getParam('enable') === 'true') ? 0 : 1; // 值为1时是正常状态
            $user->ga_enable = ($request->getParam('ga_enable') === 'true') ? 1 : 0; // 值为0时是关闭状态
            $user->limit_order = ($request->getParam('limit_order') === 'true') ? 1 : 0; // 值为1时限制下单
            $user->force_allow_invite = ($request->getParam('force_allow_invite') === 'true') ? 1 : 0; // 值为1时忽略设置的邀请限制
            if ($request->getParam('reset_user_passwd') !== '') {
                $user->pass = Hash::passwordHash($request->getParam('reset_user_passwd'));
            }
            // 检查字段
            if (!self::checkValidTime($user->expire_in) || !self::checkValidTime($user->class_expire)) {
                throw new \Exception('时间解析错误，请检查后重试');
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
            'msg' => '修改成功',
        ]);
    }

    public function createNewUser($request, $response, $args)
    {
        $email = $request->getParam('email');
        $ref_by = $request->getParam('ref_by');
        $password = $request->getParam('password');
        $email_notify = (int) $request->getParam('email_notify');
        $dispense_product = (int) $request->getParam('dispense_product');

        try {
            if ($email === '') {
                throw new \Exception('请填写邮箱');
            }
            if (!Tools::emailCheck($email)) {
                throw new \Exception('邮箱格式不正确');
            }
            $exist = User::where('email', $email)->first();
            if ($exist !== null) {
                throw new \Exception('此邮箱已注册');
            }
            if ($password === '') {
                $password = Tools::genRandomChar(10);
            }
            if ($email_notify === 1) {
                if (Setting::obtain('mail_driver') === 'none') {
                    throw new \Exception('没有有效的发信配置');
                }
            }
            AuthController::registerHelper('user', $email, $password, '', '1', '', 0, 'null');
            if ($email_notify === 1) {
                $subject = $_ENV['appName'] . ' - 您的账户已创建';
                $text = '请在 ' . $_ENV['baseUrl'] . ' 使用以下信息登录：'
                    . '<br/>账户：' . $email
                    . '<br/>密码：' . $password
                    . '<br/>'
                    . '<br/>建议您登录后前往 <b>资料修改</b> 页面重新设定登录密码。如需帮助，可通过工单系统联系我们'
                    . '<br/>';
                Mail::send($email, $subject, 'newuser.tpl', 'system', [
                    'text' => $text,
                ], []);
            }
            if ($dispense_product !== 0) {
                $user = User::where('email', $email)->first();
                $product = Product::find($dispense_product);
                $product_content = json_decode($product->content, true);
                foreach ($product_content as $key => $value) {
                    switch ($key) {
                        case 'product_time':
                            $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + ($value * 86400));
                            break;
                        case 'product_traffic':
                            $user->transfer_enable += $value * 1073741824;
                            break;
                        case 'product_class':
                            $user->class = $value;
                            break;
                        case 'product_class_time':
                            $user->class_expire = $user->expire_in;
                            break;
                        case 'product_speed':
                            $user->node_speedlimit = $value;
                            break;
                        case 'product_device':
                            $user->node_connector = $value;
                            break;
                    }
                }
                $user->save();
            }
            if ($ref_by !== '') {
                if (Tools::emailCheck($ref_by)) {
                    $invite_user = User::where('email', $ref_by)->first();
                    if ($invite_user === null) {
                        throw new \Exception('没有找到此邀请人');
                    }
                    $ref_by = $invite_user->id;
                }
                $user = User::where('email', $email)->first();
                $user->ref_by = (int) $ref_by;
                $user->save();
            }
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功',
        ]);
    }
}
