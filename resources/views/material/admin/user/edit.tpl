{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">#{$edit_user->id}</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">用户编辑</span>
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
                <div class="col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">基础信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">注册邮箱</label>
                                <div class="col">
                                    <input id="email" type="email" class="form-control" value="{$edit_user->email}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">用户昵称</label>
                                <div class="col">
                                    <input id="user_name" type="text" class="form-control"
                                        value="{$edit_user->user_name}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">私有备注</label>
                                <div class="col">
                                    <input id="remark" type="text" class="form-control" value="{$edit_user->remark}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">重置新密码</label>
                                <div class="col">
                                    <input id="reset_user_passwd" type="text" class="form-control"
                                        placeholder="若需为此用户重置密码, 填写此栏">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">账户余额</label>
                                <div class="col">
                                    <input id="money" type="number" step="0.1" class="form-control"
                                        value="{$edit_user->money}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">单端口多用户承载端口</label>
                                <div class="col">
                                    <select id="is_multi_user" class="col form-select">
                                        <option value="0">非单端口多用户承载端口</option>
                                        <option value="1">混淆式单端口多用户承载端口</option>
                                        <option value="2">协议式单端口多用户承载端口</option>
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
                                            <span class="col">管理员权限</span>
                                            <span class="col-auto">
                                                <label class="form-check form-check-single form-switch">
                                                    <input id="is_admin" class="form-check-input" type="checkbox"
                                                        {if $edit_user->is_admin == 1}checked="" {/if}>
                                                </label>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="row">
                                            <span class="col">封禁用户</span>
                                            <span class="col-auto">
                                                <label class="form-check form-check-single form-switch">
                                                    <input id="enable" class="form-check-input" type="checkbox"
                                                        {if $edit_user->enable == 0}checked="" {/if}>
                                                </label>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="row">
                                            <span class="col">两步认证</span>
                                            <span class="col-auto">
                                                <label class="form-check form-check-single form-switch">
                                                    <input id="ga_enable" class="form-check-input" type="checkbox"
                                                        {if $edit_user->ga_enable == 1}checked="" {/if}>
                                                </label>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">其他信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">流量限制 (GB)</label>
                                <div class="col">
                                    <input id="transfer_enable" type="text" class="form-control"
                                        value="{$edit_user->enableTrafficInGB()}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">已用流量 (GB)</label>
                                <div class="col">
                                    <input id="usedTraffic" type="text" class="form-control"
                                        value="{$edit_user->usedTraffic()}" disabled />
                                </div>
                            </div>
                            <div class="hr-text">
                                <span>邀请注册</span>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">可用邀请数量</label>
                                <div class="col">
                                    <input id="invite_num" type="text" class="form-control"
                                        value="{$edit_user->invite_num}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">邀请人</label>
                                <div class="col">
                                    <input id="ref_by" type="text" class="form-control" value="{$edit_user->ref_by}">
                                </div>
                            </div>
                            <div class="hr-text">
                                <span>划分与时间设置</span>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">等级过期时间</label>
                                <div class="col">
                                    <input id="class_expire" type="text" class="form-control"
                                        value="{$edit_user->class_expire}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">账户过期时间</label>
                                <div class="col">
                                    <input id="expire_in" type="text" class="form-control"
                                        value="{$edit_user->expire_in}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mb-3 col-6">
                                    <label class="form-label col-12 col-form-label">节点群组</label>
                                    <div class="col">
                                        <input id="node_group" type="text" class="form-control"
                                            value="{$edit_user->node_group}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 col-6">
                                    <label class="form-label col-12 col-form-label">账户等级</label>
                                    <div class="col">
                                        <input id="class" type="text" class="form-control" value="{$edit_user->class}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 col-6">
                                    <label class="form-label col-12 col-form-label">速度限制 (Mbps)</label>
                                    <div class="col">
                                        <input id="node_speedlimit" type="text" class="form-control"
                                            value="{$edit_user->node_speedlimit}">
                                    </div>
                                </div>
                                <div class="form-group mb-3 col-6">
                                    <label class="form-label col-12 col-form-label">链接设备限制</label>
                                    <div class="col">
                                        <input id="node_connector" type="text" class="form-control"
                                            value="{$edit_user->node_connector}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">ShadowSocks 设置</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">端口</label>
                                <div class="col">
                                    <input id="port" type="text" class="form-control" value="{$edit_user->port}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">密码</label>
                                <div class="col">
                                    <input id="passwd" type="text" class="form-control" value="{$edit_user->passwd}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">加密</label>
                                <div class="col">
                                    <input id="method" type="text" class="form-control" value="{$edit_user->method}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">协议</label>
                                <div class="col">
                                    <input id="protocol" type="text" class="form-control"
                                        value="{$edit_user->protocol}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">协议参数</label>
                                <div class="col">
                                    <input id="protocol_param" type="text" class="form-control"
                                        value="{$edit_user->protocol_param}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">混淆方式</label>
                                <div class="col">
                                    <input id="obfs" type="text" class="form-control" value="{$edit_user->obfs}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">混淆参数</label>
                                <div class="col">
                                    <input id="obfs_param" type="text" class="form-control"
                                        value="{$edit_user->obfs_param}">
                                </div>
                            </div>
                            <div class="hr-text">
                                <span>访问限制</span>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">IP / CIDR</label>
                                <div class="col">
                                    <textarea id="forbidden_ip" class="col form-control"
                                        rows="2">{$edit_user->get_forbidden_ip()}</textarea>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">PORT</label>
                                <div class="col">
                                    <textarea id="forbidden_port" class="col form-control"
                                        rows="2">{$edit_user->get_forbidden_port()}</textarea>
                                </div>
                            </div>
                            <blockquote class="blockquote">
                                <p>上方 textarea 写法均为一行一个</p>
                            </blockquote>
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
    $("#is_multi_user").prop('value', '{$edit_user->is_multi_user}');

    $("#save_changes").click(function() {
        $.ajax({
            url: '/admin/user/{$edit_user->id}',
            type: 'PUT',
            dataType: "json",
            data: {
                {foreach $field as $key}
                    {$key}: $('#{$key}').val(),
                {/foreach}
                reset_user_passwd: $('#reset_user_passwd').val(),
                is_admin: $("#is_admin").is(":checked"),
                enable: $("#enable").is(":checked"),
                ga_enable: $("#ga_enable").is(":checked"),
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