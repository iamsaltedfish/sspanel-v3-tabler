<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
use App\Models\Statistics;

class NodeController extends AdminController
{
    public static function page()
    {
        $details = [
            'route' => 'node',
            'title' => [
                'title' => '节点列表',
                'subtitle' => '系统中所有的节点',
            ],
            'field' => [
                'id' => '#',
                'name' => '名称',
                'server' => '地址',
                'sort' => '类型',
                'traffic_rate' => '倍率',
                'node_class' => '等级',
                'node_group' => '组别',
                'node_bandwidth_limit' => '流量限制',
                'node_bandwidth' => '已用流量',
                'bandwidthlimit_resetday' => '重置日',
            ],
            'create_dialog' => [
                [
                    'id' => 'name',
                    'info' => '名称',
                    'type' => 'input',
                    'placeholder' => '',
                ],
                [
                    'id' => 'server',
                    'info' => '地址',
                    'rows' => '5',
                    'type' => 'textarea',
                    'placeholder' => '',
                ],
                [
                    'id' => 'node_ip',
                    'info' => '节点IP',
                    'type' => 'input',
                    'placeholder' => '若留空, 将自动识别解析',
                ],
                [
                    'id' => 'traffic_rate',
                    'info' => '流量倍率',
                    'type' => 'input',
                    'default' => 1,
                    'placeholder' => '',
                ],
                [
                    'id' => 'mu_only',
                    'info' => '单端口多用户',
                    'type' => 'select',
                    'select' => [
                        '-1' => '只启用普通端口',
                        '1' => '只启用单端口多用户',
                    ],
                ],
                [
                    'id' => 'type',
                    'info' => '状态',
                    'type' => 'select',
                    'select' => [
                        '1' => '显示',
                        '0' => '隐藏',
                    ],
                ],
                [
                    'id' => 'remark',
                    'info' => '私有备注',
                    'type' => 'input',
                    'placeholder' => '仅管理员可见',
                ],
                [
                    'id' => 'info',
                    'info' => '公有备注',
                    'type' => 'input',
                    'placeholder' => '用户可见',
                ],
                [
                    'id' => 'sort',
                    'info' => '节点类型',
                    'type' => 'select',
                    'select' => [
                        '11' => 'V2Ray',
                        '14' => 'Trojan',
                        '0' => 'ShadowSocks',
                        '1' => 'ShadowSocksR',
                        '9' => 'ShadowsocksR 单端口多用户（旧）',
                    ],
                ],
                [
                    'id' => 'node_class',
                    'info' => '等级',
                    'type' => 'input',
                    'default' => 0,
                    'placeholder' => '',
                ],
                [
                    'id' => 'node_group',
                    'info' => '组别',
                    'type' => 'input',
                    'default' => 0,
                    'placeholder' => '',
                ],
                [
                    'id' => 'node_bandwidth_limit',
                    'info' => '流量限制 (GB)',
                    'type' => 'input',
                    'default' => 0,
                    'placeholder' => '',
                ],
                [
                    'id' => 'bandwidthlimit_resetday',
                    'info' => '流量重置日',
                    'type' => 'input',
                    'default' => date("d"),
                    'placeholder' => '',
                ],
                [
                    'id' => 'node_speedlimit',
                    'info' => '速率限制 (Mbps)',
                    'type' => 'input',
                    'default' => 0,
                    'placeholder' => '',
                ],
                [
                    'id' => 'node_connector',
                    'info' => '设备限制',
                    'type' => 'input',
                    'default' => 0,
                    'placeholder' => '',
                ],
            ],
            'update_field' => [
                'name',
                'server',
                'node_ip',
                'traffic_rate',
                'sort',
                'mu_only',
                //'type', //checkbox
                'remark',
                'info',
                'node_class',
                'node_group',
                'node_bandwidth',
                'node_bandwidth_limit',
                'bandwidthlimit_resetday',
                'node_speedlimit',
                'node_connector',
            ],
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = Node::orderBy('id', 'desc')->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/node/index.tpl')
        );
    }

    public function add($request, $response, $args)
    {
        try {
            $node = new Node();
            $field = self::page()['create_dialog'];
            foreach ($field as $key) {
                $k = $key['id'];
                $node->$k = $request->getParam($k);
            }
            // 检查
            if ($node->name === '') {
                throw new \Exception('请设置名称');
            }
            if ($node->server === '') {
                throw new \Exception('请设置节点地址');
            }
            if ($node->info === '') {
                throw new \Exception('请设置公有备注');
            }
            if ($node->remark === '') {
                throw new \Exception('请设置私有备注');
            }
            // 特殊处理一些字段
            $node->node_bandwidth_limit *= 1073741824;
            $node->server = trim($node->server);
            $node->custom_config = '{}';
            $node->status = 'running';
            // 校验服务器地址
            if ($node->node_ip === '') {
                $split = explode(';', $node->server);
                if (!$node->changeNodeIp($split[0])) {
                    throw new \Exception('未能正确解析域名');
                }
            } else {
                if (!$node->changeNodeIp($node->node_ip)) {
                    throw new \Exception('未能正确解析域名或识别IP');
                }
            }
            $node->save();
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

    public function encode(string $item, int $node_id, bool $offset = false): array
    {
        $items = Statistics::where('item', $item)
            ->where('node_id', $node_id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        $chart_x = [];
        $chart_y = [];

        foreach ($items as $record) {
            $timestamp = ($offset) ? $record->created_at - 86400 : $record->created_at;
            $chart_x[] = "'" . date('m-d', $timestamp) . "'";
            $chart_y[] = round($record->value / 1024, 2);
        }

        $result = [
            'x' => array_reverse($chart_x),
            'y' => array_reverse($chart_y),
        ];

        return $result;
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $node = Node::find($id);
        $traffic_chart = self::encode('node_traffic', $id, true);

        $charts = [
            'traffic' => [
                'element_id' => 'total-traffic',
                'series_name' => '流量',
                'x' => $traffic_chart['x'],
                'y' => $traffic_chart['y'],
            ],
        ];

        return $response->write(
            $this->view()
                ->assign('node', $node)
                ->assign('charts', $charts)
                ->assign('traffic_chart', $traffic_chart)
                ->assign('field', self::page()['update_field'])
                ->display('admin/node/edit.tpl')
        );
    }

    public function update($request, $response, $args)
    {
        try {
            $id = $args['id'];
            $node = Node::find($id);
            $field = self::page()['update_field'];
            foreach ($field as $key) {
                $node->$key = $request->getParam($key);
            }
            // 检查
            if ($node->name === '') {
                throw new \Exception('请设置名称');
            }
            if ($node->server === '') {
                throw new \Exception('请设置节点地址');
            }
            if ($node->info === '') {
                throw new \Exception('请设置公有备注');
            }
            if ($node->remark === '') {
                throw new \Exception('请设置私有备注');
            }
            // 特殊处理一些字段
            $node->node_bandwidth *= 1073741824;
            $node->node_bandwidth_limit *= 1073741824;
            $node->server = trim($node->server);
            $node->type = ($request->getParam('type') === 'true') ? 1 : 0;
            $node->save();
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

    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $node = Node::find($id);

        if (!$node->delete()) {
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

    public function copy($request, $response, $args)
    {
        try {
            $source_id = $request->getParam('id');
            $source_node = Node::find($source_id);
            $new_node = new Node();
            // https://laravel.com/docs/9.x/eloquent#replicating-models
            $new_node = $source_node->replicate([
                'node_bandwidth',
                'bandwidthlimit_resetday',
            ]);
            $new_node->name .= ' (副本)';
            $new_node->node_bandwidth = 0;
            $new_node->bandwidthlimit_resetday = date("d");
            $new_node->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '复制成功',
        ]);
    }
}
