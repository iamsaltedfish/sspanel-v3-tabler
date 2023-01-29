<?php

namespace App\Controllers\User;

use App\Controllers\NewLinkController;
use App\Controllers\UserController;
use App\Models\Node;

class NodeController extends UserController
{
    public function serverList($request, $response, $args)
    {
        $user = $this->user;
        $user_group = ($user->node_group !== 0 ? [0, $user->node_group] : [0]);
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
}
