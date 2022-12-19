<?php
namespace App\Controllers\User;

use App\Controllers\NewLinkController;
use App\Controllers\UserController;
use App\Models\Node;
use App\Models\User;
use App\Utils\Tools;
use App\Utils\URL;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class NodeController extends UserController
{
    public function serverList($request, $response, $args)
    {
        $user = $this->user;
        $user_group = ($user->node_group != 0 ? [0, $user->node_group] : [0]);
        $servers = Node::where('type', 1)
            ->where('sort', '!=', '9') // 我也不懂为什么
            ->whereIn('node_group', $user_group) // 筛选用户所在分组的服务器
            ->orderBy('name', 'asc')
            ->get();

        $class = Node::select('node_class')
            ->orderBy('node_class', 'asc')
            ->distinct()
            ->get();

        $min_node_class = min($class->toArray())['node_class'];

        $copy_content = [];
        $all_v2ray_node = '';
        $all_trojan_node = '';
        foreach ($servers as $server) {
            switch ($server->parsing_mode) {
                case 'v2ray_ws':
                case 'v2ray_ws_tls':
                    $is_tls = ($server->parsing_mode === 'v2ray_ws') ? false : true;
                    $encode_content = NewLinkController::parseV2rayWebSocket($server, $user->uuid, $is_tls);
                    $all_v2ray_node .= $encode_content;
                    break;
                case 'trojan_grpc':
                    $encode_content = NewLinkController::parseTrojanGrpc($server, $user->uuid);
                    $all_trojan_node .= $encode_content;
                    break;
            }

            $copy_content[$server->id] = $encode_content;
        }

        $copy_content['all_v2ray_node'] = $all_v2ray_node;
        $copy_content['all_trojan_node'] = $all_trojan_node;

        return $response->write(
            $this->view()
                ->assign('class', $class)
                ->assign('servers', $servers)
                ->assign('copy_content', $copy_content)
                ->assign('min_node_class', $min_node_class)
                ->display('user/node/servers.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function user_node_page($request, $response, $args): ResponseInterface
    {
        $user = $this->user;
        $query = Node::query();
        $query->where('type', 1)->whereNotIn('sort', [9]);
        if (!$user->is_admin) {
            $group = ($user->node_group != 0 ? [0, $user->node_group] : [0]);
            $query->whereIn('node_group', $group);
        }
        $nodes = $query->orderBy('node_class')->orderBy('name')->get();
        $all_node = [];
        foreach ($nodes as $node) {
            /** @var Node $node */

            $array_node = [];
            $array_node['id'] = $node->id;
            $array_node['name'] = $node->name;
            $array_node['class'] = $node->node_class;
            $array_node['info'] = $node->info;
            $array_node['flag'] = 'unknown.png';
            $array_node['online_user'] = $node->get_node_online_user_count();
            $array_node['online'] = $node->get_node_online_status();
            $array_node['latest_load'] = $node->get_node_latest_load_text();
            $array_node['traffic_rate'] = $node->traffic_rate;
            $array_node['status'] = $node->status;
            $array_node['traffic_used'] = (int) Tools::flowToGB($node->node_bandwidth);
            $array_node['traffic_limit'] = (int) Tools::flowToGB($node->node_bandwidth_limit);
            $array_node['bandwidth'] = $node->get_node_speedlimit();

            $all_connect = [];
            if (in_array($node->sort, [0])) {
                if ($node->mu_only != 1) {
                    $all_connect[] = 0;
                }
                if ($node->mu_only != -1) {
                    $mu_node_query = Node::query();
                    $mu_node_query->where('sort', 9)->where('type', '1');
                    if (!$user->is_admin) {
                        $mu_node_query->where('node_class', '<=', $user->class)->whereIn('node_group', $group);
                    }
                    $mu_nodes = $mu_node_query->get();
                    foreach ($mu_nodes as $mu_node) {
                        if (User::where('port', $mu_node->server)->where('is_multi_user', '<>', 0)->first() != null) {
                            $all_connect[] = $node->getOffsetPort($mu_node->server);
                        }
                    }
                }
            } else {
                $all_connect[] = 0;
            }
            $array_node['connect'] = $all_connect;

            $all_node[$node->node_class + 1000][] = $array_node;
        }

        return $response->write(
            $this->view()
                ->assign('nodes', $all_node)
                ->display('user/node/index.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function user_node_ajax($request, $response, $args): ResponseInterface
    {
        $id = $args['id'];
        $point_node = Node::find($id);
        $prefix = explode(' - ', $point_node->name);
        return $response->write(
            $this->view()
                ->assign('point_node', $point_node)
                ->assign('prefix', $prefix[0])
                ->assign('id', $id)
                ->display('user/node/nodeajax.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function user_node_info($request, $response, $args): ResponseInterface
    {
        $user = $this->user;
        $node = Node::find($args['id']);
        if ($node == null) {
            return $response->write('非法访问');
        }
        if (!$user->is_admin) {
            if ($user->node_group != $node->node_group && $node->node_group != 0) {
                return $response->write('无权查看该分组的节点');
            }
            if ($user->class < $node->node_class) {
                return $response->write('无权查看该等级的节点');
            }
        }
        switch ($node->sort) {
            case 0:
                return $response->write(
                    $this->view()
                        ->assign('node', $node)
                        ->assign('mu', $request->getQueryParams()['ismu'])
                        ->registerClass('URL', URL::class)
                        ->display('user/node/node_ss_ssr.tpl')
                );
            case 11:
                $server = $node->getV2RayItem($user);
                $nodes = [
                    'url' => URL::getV2Url($user, $node),
                    'info' => [
                        '连接地址：' => $server['add'],
                        '连接端口：' => $server['port'],
                        'UUID：' => $user->uuid,
                        'AlterID：' => $server['aid'],
                        '传输协议：' => $server['net'],
                    ],
                ];
                if ($server['net'] == 'ws') {
                    $nodes['info']['PATH：'] = $server['path'];
                    $nodes['info']['HOST：'] = $server['host'];
                }
                if ($server['net'] == 'kcp') {
                    $nodes['info']['伪装类型：'] = $server['type'];
                }
                if ($server['tls'] == 'tls') {
                    $nodes['info']['TLS：'] = 'TLS';
                }
                return $response->write(
                    $this->view()
                        ->assign('node', $nodes)
                        ->display('user/node/node_v2ray.tpl')
                );
            case 13:
                $server = $node->getV2RayPluginItem($user);
                if ($server != null) {
                    $nodes = [
                        'url' => URL::getItemUrl($server, 1),
                        'info' => [
                            '连接地址：' => $server['address'],
                            '连接端口：' => $server['port'],
                            '加密方式：' => $server['method'],
                            '连接密码：' => $server['passwd'],
                            '混淆方式：' => $server['obfs'],
                            '混淆参数：' => $server['obfs_param'],
                        ],
                    ];
                } else {
                    $nodes = [
                        'url' => '',
                        'info' => [
                            '您的加密方式非 AEAD 系列' => '无法使用此节点.',
                        ],
                    ];
                }
                return $response->write(
                    $this->view()
                        ->assign('node', $nodes)
                        ->display('user/node/node_ss_v2ray_plugin.tpl')
                );
            case 14:
                $server = $node->getTrojanItem($user);
                $nodes = [
                    'url' => URL::get_trojan_url($user, $node),
                    'info' => [
                        '连接地址：' => $server['address'],
                        '连接端口：' => $server['port'],
                        '连接密码：' => $server['passwd'],
                    ],
                ];
                if ($server['host'] != $server['address']) {
                    $nodes['info']['HOST&PEER：'] = $server['host'];
                }
                return $response->write(
                    $this->view()
                        ->assign('node', $nodes)
                        ->display('user/node/node_trojan.tpl')
                );
            default:
                return $response->write(404);
        }
    }
}
