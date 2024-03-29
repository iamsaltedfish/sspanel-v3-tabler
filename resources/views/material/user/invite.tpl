{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span class="home-title">邀请注册</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">查看邀请注册链接和邀请返利记录</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">邀请规则</h3>
                            <ul>
                                <li>邀请注册的用户在账单确认后，您可获得其账单金额的 <code>{$config['code_payback'] * 100} %</code>
                                    作为返利</li>
                                <li>请不要注册小号来自己邀请自己，相关订单返利可能会被认定为存疑</li>
                                <li>具体邀请返利规则请查看公告，或通过工单系统询问管理员</li>
                                <li>部分商品的返利比例可能不遵循上面的比例</li>
                                <li>若邀请注册的用户申请退款，返利金额也将撤销</li>
                            </ul>
                            <p>您目前通过邀请好友获得的总返利为 <code>{sprintf("%.2f", $paybacks_sum)}</code> 元</p>
                            {if count($table) > 0}
                                <a href="#" data-bs-toggle="modal" data-bs-target="#rebate-comparison-table">查看商品返利对照表</a>
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        {if $invite_permissions['result'] === true || $user->force_allow_invite === 1}
                            <div class="card-body">
                                <h3 class="card-title">邀请链接</h3>
                                {if $user->invite_num >= 0}
                                    <p>邀请链接可用次数：<code>{$user->invite_num}</code></p>
                                {/if}
                                {if $config['disposable_invite_code'] === true}
                                    <p style="color: red">当前邀请链接为一次性，使用后，请在此页面查看新的邀请链接</p>
                                {/if}
                                <input class="form-control" value="{$invite_url}" disabled />
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    <a id="reset-url" class="btn text-red btn-link">重置</a>
                                    <a data-clipboard-text="{$invite_url}" class="copy btn btn-primary ms-auto">复制</a>
                                </div>
                            </div>
                        {else}
                            <div class="card-body">
                                <h3 class="card-title">邀请链接</h3>
                                <p>您因为不满足下列条件，所以暂无邀请权限。过段时间再来看看吧</p>
                                <ul>
                                    <li>{$invite_permissions['reason']}</li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    <button class="btn text-red btn-link" disabled>重置</button>
                                    <button class="btn btn-primary ms-auto" disabled>复制</button>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">返利记录</h3>
                        </div>
                        {if $paybacks->count() !== 0}
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>邀请用户昵称</th>
                                            <th>返利金额</th>
                                            <th>系统自动审核</th>
                                            <th>关联订单状态</th>
                                            <th>返利时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach $paybacks as $payback}
                                            <tr>
                                                <td>{$payback->id}</td>
                                                {if $payback->user()!=null}
                                                    <td>{$payback->user()->user_name}</td>
                                                {else}
                                                    <td>已注销</td>
                                                {/if}
                                                <td>{$payback->ref_get} 元</td>
                                                <td>{$payback->fraud_detect}</td>
                                                <td>{($payback->associated_order_status === 1) ? '未退' : '已退'}</td>
                                                <td>{$payback->datetime}</td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            <div class="card-body">
                                <p>没有找到记录</p>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {if count($table) > 0}
        <div class="modal modal-blur fade" id="rebate-comparison-table" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">商品返利对照表</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>商品</th>
                                            <th>金额</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach $table as $name => $price}
                                            <tr>
                                                <td>{$name}</td>
                                                <td>{sprintf("%.2f", $price)}</td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">确认</button>
                    </div>
                </div>
            </div>
        </div>
    {/if}

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
                                <a id="success-confirm" href="#" class="btn w-100" data-bs-dismiss="modal">
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
        $("td:contains('通过')").css("color", "green");
        $("td:contains('未退')").css("color", "green");
        $("td:contains('已退')").css("color", "purple");
        $("td:contains('存疑')").css("color", "orange");

        var clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            $('#success-message').text('已复制到剪切板');
            $('#success-dialog').modal('show');
        });

        $("#success-confirm").click(function() {
            location.reload();
        });

        $("#reset-url").click(function() {
            $.ajax({
                type: "PUT",
                url: "/user/invite",
                dataType: "json",
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });
    </script>
{include file='user/tabler_footer.tpl'}