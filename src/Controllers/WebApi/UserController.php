<?php

namespace App\Controllers\WebApi;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\Ip;
use App\Models\Node;
use App\Models\NodeOnlineLog;
use App\Models\User;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;

class UserController extends BaseController
{
    public function index($request, $response, $args): ResponseInterface
    {
        $node_id = (int) $request->getQueryParam('node_id', 0);

        if ($node_id === 0) {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        } else {
            $node = Node::where('id', '=', $node_id)->first();
            if ($node === null) {
                return $response->withJson([
                    'ret' => 0,
                ]);
            }
        }
        $node->node_heartbeat = time();
        $node->save();

        // 节点流量耗尽则返回 null
        if (($node->node_bandwidth_limit !== 0) && $node->node_bandwidth_limit < $node->node_bandwidth) {
            $users = null;
            return $response->withJson([
                'ret' => 1,
                'data' => $users,
            ]);
        }

        /*
         * 1. 请不要把管理员作为单端口承载用户
         * 2. 请不要把真实用户作为单端口承载用户
         */
        $users_raw = User::where(
            static function ($query) use ($node) {
                $query->where(
                    static function ($query1) use ($node) {
                        if ($node->node_group !== 0) {
                            $query1->where('class', '>=', $node->node_class)
                                ->where('node_group', '=', $node->node_group);
                        } else {
                            $query1->where('class', '>=', $node->node_class);
                        }
                    }
                )->orwhere('is_admin', 1);
            }
        )->where('enable', 1)->where('expire_in', '>', date('Y-m-d H:i:s'))->get();

        if ($node->sort === 14) {
            $key_list = ['node_speedlimit', 'id', 'node_connector', 'uuid', 'alive_ip'];
        } elseif ($node->sort === 11) {
            $key_list = ['node_speedlimit', 'id', 'node_connector', 'uuid', 'alive_ip'];
        } else {
            $key_list = [
                'method', 'obfs', 'obfs_param', 'protocol', 'protocol_param', 'node_speedlimit',
                'is_multi_user', 'id', 'port', 'passwd', 'node_connector', 'alive_ip',
            ];
        }

        $alive_ip = (new \App\Models\Ip())->getUserAliveIpCount();
        $users = [];
        foreach ($users_raw as $user_raw) {
            $user_raw_id = strval($user_raw->id);
            if (isset($alive_ip[$user_raw_id]) && $user_raw->node_connector !== 0) {
                $user_raw->alive_ip = $alive_ip[$user_raw_id];
            }
            if ($user_raw->transfer_enable <= $user_raw->u + $user_raw->d) {
                if ($_ENV['keep_connect'] === true) {
                    // 流量耗尽用户限速至 1Mbps
                    $user_raw->node_speedlimit = 1;
                } else {
                    continue;
                }
            }
            $user_raw = Tools::keyFilter($user_raw, $key_list);
            $users[] = $user_raw;
        }

        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($users);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson([
            'ret' => 1,
            'data' => $users,
        ]);
    }

    public function getTraffic($request, $response, $args)
    {
        $res = [
            'ret' => 0,
            'data' => 'The interface for the node to report the traffic usage should use the post request. If you see this message, check the server. This problem may occur in the debian system, you can try to replace the ubuntu system and try again.',
        ];
        return $response->withJson($res);
    }

    public function addTraffic($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $this_time_total_bandwidth = 0;
        $node_id = (int) $params['node_id'];
        if ($node_id === 0) {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                $u = $log['u'];
                $d = $log['d'];
                $user_id = $log['user_id'];
                $user = User::find($user_id);

                if ($user === null) {
                    continue;
                }

                $user->t = time();
                $user->u += $u * $node->traffic_rate;
                $user->d += $d * $node->traffic_rate;
                $this_time_total_bandwidth += $u + $d;
                if (!$user->save()) {
                    $res = [
                        'ret' => 0,
                        'data' => 'update failed',
                    ];
                    return $response->withJson($res);
                }
            }
        }

        $node->node_bandwidth += $this_time_total_bandwidth;
        $node->save();

        $online_log = new NodeOnlineLog();
        $online_log->node_id = $node_id;
        $online_log->online_user = count($data);
        $online_log->log_time = time();
        $online_log->save();

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }

    public function addAliveIp($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
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
        if (count($data) > 0) {
            foreach ($data as $log) {
                $ip = $log['ip'];
                $userid = $log['user_id'];

                // log
                $ip_log = new Ip();
                $ip_log->userid = $userid;
                $ip_log->nodeid = $node_id;
                $ip_log->ip = $ip;
                $ip_log->datetime = time();
                $ip_log->save();
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }

    public function addDetectLog($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
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

        if (count($data) > 0) {
            foreach ($data as $log) {
                $list_id = $log['list_id'];
                $user_id = $log['user_id'];

                // log
                $detect_log = new DetectLog();
                $detect_log->user_id = $user_id;
                $detect_log->list_id = $list_id;
                $detect_log->node_id = $node_id;
                $detect_log->datetime = time();
                $detect_log->save();
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }
}
