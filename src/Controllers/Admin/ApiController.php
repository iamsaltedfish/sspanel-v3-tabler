<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Node;
use Psr\Http\Message\ResponseInterface;

class ApiController extends BaseController
{
    public function getNodeList($request, $response, $args): ResponseInterface
    {
        return $response->withJson([
            "ret" => 1,
            "nodes" => Node::where('type', 1)->orderBy('name', 'asc')->get(),
        ]);
    }

    public function getNodeRelayList($request, $response, $args)
    {
        $text = '';
        $nodes = Node::where('server', 'like', '%relayserver%')
            ->where('type', '1')
            ->where('sort', '11')
            ->get();

        foreach ($nodes as $node) {
            $text .= $node->server . PHP_EOL;
        }

        return $text;
    }

    public function getNodeInfo($request, $response, $args): ResponseInterface
    {
        $node = Node::find($args['id']);

        return $response->withJson([
            "ret" => 1,
            "node" => $node,
        ]);
    }

    public function getNodeStatus($request, $response, $args): ResponseInterface
    {
        $node = Node::find($args['id']);
        if ($node === null) {
            return $response->withJson([
                "ret" => 0,
                "msg" => 'This node id was not found.',
            ]);
        }
        return $response->withJson([
            "ret" => 1,
            "data" => $node->isNodeOnline() ? 'online' : 'offline',
        ]);
    }

    public function getNodeId($request, $response, $args): ResponseInterface
    {
        $condition = [];
        $param = $args['ip'];
        $requests_index = $request->getParam('requests_index');
        $requests_node_sort = $request->getParam('requests_node_sort');

        // 处理查询索引
        if ($requests_index === '' || $requests_index === 'node_ip') {
            array_push($condition, ['node_ip', '=', $param]);
        } elseif ($requests_index === 'hostname') {
            array_push($condition, ['server', 'like', '%' . $param . '%']);
        } else {
            return $response->withJson([
                "ret" => 0,
                "msg" => 'Not supported query index field.',
            ]);
        }
        // 处理可能会指定的节点类型
        if ($requests_node_sort !== '') {
            // 11 => V2Ray, 14 => Trojan
            array_push($condition, ['sort', '=', $requests_node_sort]);
        }
        // 查询
        $node = Node::where($condition)->get();
        // 返回
        if ($node->count() === 0) {
            return $response->withJson([
                "ret" => 0,
                "msg" => 'No data matching the criteria.',
            ]);
        }

        return $response->withJson([
            "ret" => 1,
            "data" => $node,
            "total" => $node->count(),
        ]);
    }

    public function ping($request, $response, $args): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'data' => 'pong',
        ]);
    }

    public function changeServicePort($request, $response, $args): ResponseInterface
    {
        $node = Node::find($args['id']);
        if (!isset($node)) {
            $node = Node::where('server', 'like', '%' . $args['id'] . '%')->first();
            if (!isset($node)) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => 'This node was not found.',
                ]);
            }
        }
        if ($node->sort !== 11) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Only nodes of type v2ray are supported.',
            ]);
        }

        $current_node_port = explode(';', $node->server);
        $current_node_port = $current_node_port[1];
        $new_node_port = $current_node_port + 2;
        $node->server = str_replace(";${current_node_port};", ";${new_node_port};", $node->server);
        $node->save();

        return $response->withJson([
            'ret' => 1,
            'data' => $new_node_port,
        ]);
    }
}
