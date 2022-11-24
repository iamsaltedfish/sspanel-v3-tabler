{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">#{$node->id}</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">节点编辑</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a id="save_changes" href="#" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                <circle cx="12" cy="14" r="2"></circle>
                                <polyline points="14 4 14 8 8 8 8 4"></polyline>
                            </svg>
                            保存
                        </a>
                        <a id="save_changes" href="#" class="btn btn-primary d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                <circle cx="12" cy="14" r="2"></circle>
                                <polyline points="14 4 14 8 8 8 8 4"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">基础信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">名称</label>
                                <div class="col">
                                    <input id="name" type="text" class="form-control" value="{$node->name}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">地址</label>
                                <div class="col">
                                    <textarea id="server" class="col form-control" rows="5">{$node->server}</textarea>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">公网地址</label>
                                <div class="col">
                                    <input id="node_ip" type="text" class="form-control" value="{$node->node_ip}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">流量倍率</label>
                                <div class="col">
                                    <input id="traffic_rate" type="text" class="form-control"
                                        value="{$node->traffic_rate}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">接入类型</label>
                                <div class="col">
                                    <select id="sort" class="col form-select">
                                        <option value="11">V2Ray</option>
                                        <option value="14">Trojan</option>
                                        <option value="0">ShadowSocks</option>
                                        <option value="1">ShadowSocksR</option>
                                        <option value="9">ShadowsocksR 单端口多用户（旧）</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">单端口多用户</label>
                                <div class="col">
                                    <select id="mu_only" class="col form-select">
                                        <option value="-1">只启用普通端口</option>
                                        <option value="1">只启用单端口多用户</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-text">
                                <span>高级选项</span>
                            </div>
                            <div class="mb-3">
                                <!-- <label class="form-label">Notification</label> -->
                                <div class="divide-y">
                                    <div>
                                        <label class="row">
                                            <span class="col">上线此节点</span>
                                            <span class="col-auto">
                                                <label class="form-check form-check-single form-switch">
                                                    <input id="type" class="form-check-input" type="checkbox"
                                                        {if $node->type == 1}checked="" {/if}>
                                                </label>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">其他信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">私有备注</label>
                                <div class="col">
                                    <input id="remark" type="text" class="form-control" value="{$node->remark}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">公有备注</label>
                                <div class="col">
                                    <input id="info" type="text" class="form-control" value="{$node->info}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">等级</label>
                                <div class="col">
                                    <input id="node_class" type="text" class="form-control" value="{$node->node_class}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">组别</label>
                                <div class="col">
                                    <input id="node_group" type="text" class="form-control" value="{$node->node_group}">
                                </div>
                            </div>
                            <div class="hr-text">
                                <span>流量设置</span>
                            </div>
                            <!-- 避免除0 -->
                            {if $node->node_bandwidth !== 0 && $node->node_bandwidth_limit !== 0}
                                <div class="mb-3">
                                    <!-- <label class="form-label">Progress</label> -->
                                    <div class="progress mb-2">
                                        <div class="progress-bar"
                                            style="width: {round($node->node_bandwidth / $node->node_bandwidth_limit * 100, 2)}%"
                                            role="progressbar" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100"
                                            aria-label="38% Complete">
                                            <span class="visually-hidden">38% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">已用流量 (GB)</label>
                                <div class="col">
                                    <input id="node_bandwidth" type="text" class="form-control"
                                        value="{round($node->node_bandwidth / 1073741824, 2)}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">可用流量 (GB)</label>
                                <div class="col">
                                    <input id="node_bandwidth_limit" type="text" class="form-control"
                                        value="{round($node->node_bandwidth_limit / 1073741824, 2)}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">流量重置日</label>
                                <div class="col">
                                    <input id="bandwidthlimit_resetday" type="text" class="form-control"
                                        value="{$node->bandwidthlimit_resetday}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">速率限制 (Mbps)</label>
                                <div class="col">
                                    <input id="node_speedlimit" type="text" class="form-control"
                                        value="{$node->node_speedlimit}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">设备限制</label>
                                <div class="col">
                                    <input id="node_connector" type="text" class="form-control"
                                        value="{$node->node_connector}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <circle cx="12" cy="12" r="9" />
                    <path d="M9 12l2 2l4 -4" />
                </svg>
                <p id="success-message" class="text-muted">成功</p>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a id="success-confirm" href="#" class="btn btn-success w-100" data-bs-dismiss="modal">
                                确认
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="fail-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 9v2m0 4v.01" />
                    <path
                        d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                </svg>
                <p id="fail-message" class="text-muted">失败</p>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                确认
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#mu_only").prop('value', '{$node->mu_only}');
    $("#sort").prop('value', '{$node->sort}');

    $("#save_changes").click(function() {
        $.ajax({
            url: '/admin/node/{$node->id}',
            type: 'PUT',
            dataType: "json",
            data: {
                {foreach $field as $key}
                    {$key}: $('#{$key}').val(),
                {/foreach}
                type: $("#type").is(":checked"),
            },
            success: function(data) {
                if (data.ret == 1) {
                    $('#success-message').text(data.msg);
                    $('#success-dialog').modal('show');
                    window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            }
        })
    });
</script>

{include file='admin/tabler_admin_footer.tpl'}