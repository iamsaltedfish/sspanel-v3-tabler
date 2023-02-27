# ApiController.php

## getNodeList
路径：`/admin/api/nodes`
方法：`GET`
参数：无
返回：所有启用的节点，按节点名称升序排列，使用 `json` 编码

## getNodeRelayList
路劲：`/admin/api/nodes/relay`
方法：`GET`
参数：无
返回：所有启用的，`server` 字段中包含关键词 `relayserver` 的 `v2ray` 节点的 `server` 字段文本，一行一个

## getNodeInfo
路径：`/admin/api/node/{id}`
方法：`GET`
参数：`id`
返回：该节点的全部字段信息，使用 `json` 编码

## getNodeStatus
路径：`/admin/api/node/{id}/status`
方法：`GET`
参数：`id`
返回：若指定 `id` 的节点不存在
```
{
    "ret": 0,
    "msg": "This node id was not found."
}
```
若存在但节点已经离线
```
{
    "ret": 1,
    "msg": "offline"
}
```
若存在且节点在线
```
{
    "ret": 1,
    "msg": "online"
}
```

## getNodeId
路径：`/admin/api/nodeid/<ipv4 or keyword>`
方法：`GET`
参数：1.请求路径中的 `ipv4 or keyword` 地址，必要
2.字段 `requests_index`，可选。不指定时，会将请求路径中的 `<ipv4 or keyword>` 认定为 `ip` 地址，对 `node_ip` 字段全词查找。指定为 `hostname` 时，会将请求路径中的 `<ipv4 or keyword>` 认定为关键词并用此对 `server` 字段进行包含查找
3.字段 `requests_node_sort`，可选。指定 `11` 时查找 `v2ray` 节点，指定 `14` 时查找 `trojan` 节点
返回：若指定的 `requests_index` 不支持
```
{
    "ret": 0,
    "msg": "Not supported query index field."
}
```
若指定的查询条件结果为空
```
{
    "ret": 0,
    "msg": "No data matching the criteria."
}
```
若查询结果不为空
```
{
    "ret": 1,
    "data" => <符合查询条件的各个节点信息>,
    "total" => <符合查询条件的节点数量>,
}
```

## changeServicePort
路径：`/admin/api/node/<param>/port`
方法：`PUT`
参数：1.请求路径中的 `param`。会用 `pamas` 对 `id` 字段查找。如果没有结果，会对 `server` 字段查找
返回：如果节点类型不是 `v2ray`
```
{
    "ret": 0,
    "msg": "Only nodes of type v2ray are supported."
}
```
如果没有匹配的节点
```
{
    "ret": 0,
    "msg": "This node was not found."
}
```
如果有匹配的节点，会更换新的服务端口（将当前服务端口向后推2），然后返回新的服务端口
```
{
    "ret": 1,
    "data": <更换的新的v2ray服务端口>
}
```
