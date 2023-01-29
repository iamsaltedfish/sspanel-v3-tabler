<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Ip;
use App\Models\LoginIp;
use App\Utils\QQWry;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;

class IpController extends AdminController
{
    public static function page()
    {
        $details = [
            'route' => 'login',
            'title' => [
                'title' => '登录记录',
                'subtitle' => '用户登录信息，仅显示最近的 ' . $_ENV['page_load_data_entry'] . ' 条记录',
            ],
            'field' => [
                'id' => '#',
                'userid' => '用户编号',
                'ip' => '公网地址',
                'ua' => '标识',
                'attribution' => '归属地',
                'datetime' => '登录时间',
                'result' => '登录结果',
            ],
            'search_dialog' => [
                [
                    'id' => 'userid',
                    'info' => '用户编号',
                    'type' => 'input',
                    'placeholder' => '',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'ip',
                    'info' => '公网地址',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'ua',
                    'info' => '浏览器标识',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'attribution',
                    'info' => '归属地',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'type',
                    'info' => '登录结果',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有结果',
                        '1' => '失败',
                        '0' => '成功',
                    ],
                    'exact' => true,
                ],
            ],
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = LoginIp::orderBy('id', 'desc')
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        foreach ($logs as $log) {
            $log->datetime = date('Y-m-d H:i:s', $log->datetime);
            $log->result = ($log->type === 0) ? '成功' : '失败';
        }

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/ip/login.tpl')
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

        $results = LoginIp::orderBy('id', 'desc')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        foreach ($results as $result) {
            $result->datetime = date('Y-m-d H:i:s', $result->datetime);
            $result->result = ($result->type === 0) ? '成功' : '失败';
        }

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    /**
     * 后台在线 IP 页面
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function alive($request, $response, $args)
    {
        $table_config = [];
        $table_config['total_column'] = [
            'id' => 'ID',
            'userid' => '用户ID',
            'user_name' => '用户名',
            'nodeid' => '节点ID',
            'node_name' => '节点名',
            'ip' => 'IP',
            'location' => '归属地',
            'datetime' => '时间',
            'is_node' => '是否为中转连接',
        ];
        $table_config['default_show_column'] = array_keys($table_config['total_column']);
        $table_config['ajax_url'] = 'alive/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/ip/alive.tpl')
        );
    }

    /**
     * 后台在线 IP 页面 AJAX
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajaxAlive($request, $response, $args)
    {
        $query = Ip::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'userid';
                }
                if (in_array($order_field, ['node_name', 'is_node'])) {
                    $order_field = 'nodeid';
                }
                if (in_array($order_field, ['location'])) {
                    $order_field = 'ip';
                }
            },
            static function ($query) {
                $query->where('datetime', '>=', time() - 60);
            }
        );

        $data = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var Ip $value */

            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['userid'] = $value->userid;
            $tempdata['user_name'] = $value->userName();
            $tempdata['nodeid'] = $value->nodeid;
            $tempdata['node_name'] = $value->nodeName();
            $tempdata['ip'] = Tools::getRealIp($value->ip);
            $tempdata['location'] = $value->location($QQWry);
            $tempdata['datetime'] = $value->datetime();
            $tempdata['is_node'] = $value->isNode();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => Ip::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
