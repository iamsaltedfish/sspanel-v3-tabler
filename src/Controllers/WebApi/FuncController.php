<?php

namespace App\Controllers\WebApi;

use App\Controllers\BaseController;
use App\Models\DetectRule;
use App\Models\Log;
use App\Models\Node;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;

class FuncController extends BaseController
{
    public function ping($request, $response, $args)
    {
        $res = [
            'ret' => 1,
            'data' => 'pong',
        ];
        return $response->withJson($res);
    }

    public function getDetectLogs($request, $response, $args): ResponseInterface
    {
        $rules = DetectRule::all();

        $res = [
            'ret' => 1,
            'data' => $rules,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($rules);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    public function getBlockip($request, $response, $args): ResponseInterface
    {
        $block_ips = [];

        $res = [
            'ret' => 1,
            'data' => $block_ips,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($block_ips);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    public function getUnblockip($request, $response, $args): ResponseInterface
    {
        $unblock_ips = [];

        $res = [
            'ret' => 1,
            'data' => $unblock_ips,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($unblock_ips);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    public function addBlockIp($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $node_id = (int) $params['node_id'];
        if ($node_id === 0) {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);
        if ($node === null) {
            $res = [
                'ret' => 0,
            ];
            return $response->withJson($res);
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }

    public function log($request, $response, $args)
    {
        $msg = $request->getParam('msg');
        $type = $request->getParam('type');
        $level = $request->getParam('level');
        $status = $request->getParam('status');
        $reporter = $request->getParam('reporter');

        $l = new Log();
        $l->type = $type;
        $l->reporter = $reporter;
        $l->level = $level;
        $l->msg = $msg;
        $l->status = ($status === '') ? 0 : $status;
        $l->created_at = time();

        if ($l->save()) {
            return $response->withJson([
                'ret' => 1,
                'data' => 'ok',
            ]);
        }
    }
}
