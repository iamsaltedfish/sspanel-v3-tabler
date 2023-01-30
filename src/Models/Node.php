<?php

namespace App\Models;

use App\Utils\Tools;

class Node extends Model
{
    protected $connection = 'default';

    protected $table = 'node';

    protected $casts = [
        'node_speedlimit' => 'float',
        'traffic_rate' => 'float',
        'mu_only' => 'int',
        'sort' => 'int',
        'type' => 'bool',
        'node_heartbeat' => 'int',
    ];

    /**
     * 节点是否显示和隐藏
     */
    public function type(): string
    {
        return $this->type ? '显示' : '隐藏';
    }

    /**
     * 获取节点 5 分钟内最新的在线人数
     */
    public function getNodeOnlineUserCount(): int
    {
        if (in_array($this->sort, [9])) {
            return -1;
        }
        $log = NodeOnlineLog::where('node_id', $this->id)
            ->where('log_time', '>', time() - 300)
            ->orderBy('id', 'desc')
            ->first();
        if ($log === null) {
            return 0;
        }
        return $log->online_user;
    }

    /**
     * 获取节点在线状态
     *
     * @return int 0 = new node OR -1 = offline OR 1 = online
     */
    public function getNodeOnlineStatus(): int
    {
        // 类型 9 或者心跳为 0
        if ($this->node_heartbeat === 0 || in_array($this->sort, [9])) {
            return 0;
        }
        return $this->node_heartbeat + 300 > time() ? 1 : -1;
    }

    /**
     * 获取节点在线状态指示颜色
     */
    public function getNodeStatusColor(): string
    {
        // 类型 9 或者心跳为 0
        if ($this->node_heartbeat === 0 || in_array($this->sort, [9])) {
            return 'orange';
        }
        if ($this->node_bandwidth_limit !== 0 && $this->node_bandwidth >= $this->node_bandwidth_limit) {
            return 'yellow';
        }
        return $this->node_heartbeat + 300 > time() ? 'green' : 'red';
    }

    /**
     * 节点是在线的
     */
    public function isNodeOnline(): ?bool
    {
        if ($this->node_heartbeat === 0) {
            return false;
        }
        return $this->node_heartbeat > time() - 300;
    }

    /**
     * 节点流量已耗尽
     */
    public function isNodeTrafficOut(): bool
    {
        return !($this->node_bandwidth_limit === 0 || $this->node_bandwidth < $this->node_bandwidth_limit);
    }

    /**
     * 节点是可用的，即流量未耗尽并且在线
     */
    public function isNodeAccessable(): bool
    {
        return $this->isNodeTrafficOut() === false && $this->isNodeOnline() === true;
    }

    /**
     * 更新节点 IP
     *
     * @param string $server_name
     */
    public function changeNodeIp(string $server_name): bool
    {
        if (!Tools::isIp($server_name)) {
            $ip = gethostbyname($server_name);
            if ($ip === '') {
                return false;
            }
        } else {
            $ip = $server_name;
        }
        $this->node_ip = $ip;
        return true;
    }

    /**
     * 获取节点 IP
     */
    public function getNodeIp(): string
    {
        $node_ip_str = $this->node_ip;
        $node_ip_array = explode(',', $node_ip_str);
        return $node_ip_array[0];
    }

    /**
     * 获取出口地址 | 用于节点IP获取的地址
     */
    public function getOutAddress(): string
    {
        return explode(';', $this->server)[0];
    }
}
