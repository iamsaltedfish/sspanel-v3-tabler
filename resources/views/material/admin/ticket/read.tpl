{include file='admin/tabler_admin_header.tpl'}

<style>
    table td {
        white-space: nowrap;
    }
</style>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">工单回复</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">你可以在这里查看历史消息并添加回复</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button href="#" class="btn btn-red d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#close_ticket_confirm_dialog">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            关闭
                        </button>
                        <button href="#" class="btn btn-red d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#close_ticket_confirm_dialog">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        <button href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#historical_order">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <line x1="9" y1="7" x2="10" y2="7"></line>
                                <line x1="9" y1="13" x2="15" y2="13"></line>
                                <line x1="13" y1="17" x2="15" y2="17"></line>
                            </svg>
                            订单
                        </button>
                        <button href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#historical_order">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <line x1="9" y1="7" x2="10" y2="7"></line>
                                <line x1="9" y1="13" x2="15" y2="13"></line>
                                <line x1="13" y1="17" x2="15" y2="17"></line>
                            </svg>
                        </button>
                        <button href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#add-reply">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            回复
                        </button>
                        <button href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#add-reply">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="h1 my-2 mb-3">#{$topic->tk_id} {$topic->title}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center my-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="divide-y">
                                {$count = '0'}
                                {$total = $discussions->count()}
                                {foreach $discussions as $discuss}
                                    <div>
                                        <div class="row">
                                            {if $discuss->user_id != '0'}
                                                <div class="col-auto">
                                                    <span class="avatar">用户</span>
                                                </div>
                                            {else}
                                                <div class="col-auto">
                                                    <span class="avatar"
                                                        style="background-image: url(/theme/tabler/static/admin.png)"></span>
                                                </div>
                                            {/if}
                                            <div class="col">
                                                <div>
                                                    {nl2br($discuss->content)}
                                                </div>
                                                <div class="text-muted my-1">{$discuss->created_at}</div>
                                            </div>
                                            <!-- 标记最新回复 -->
                                            {$count = $count + 1}
                                            {if $count == $total}
                                                <div class="col-auto align-self-center">
                                                    <div class="badge bg-primary"></div>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p style="line-height: 24px;">
                                提交用户：<code>{$tk_user->id}</code>
                                ，昵称：<code>{$tk_user->user_name}</code>
                                ，注册邮箱：<code>{$tk_user->email}</code>
                                ，<a href="/admin/user/{$tk_user->id}/edit">编辑用户</a>
                            </p>
                            <p style="line-height: 24px;">
                                用户等级：<code>{$tk_user->class}</code>
                                ，等级时间：<code>{$tk_user->class_expire}</code>
                                ，到期时间：<code>{$tk_user->expire_in}</code>
                                ，流量限制：<code>{round($tk_user->transfer_enable / 1073741824, 2)}</code> GB
                                ，历史用量：<code>{round($tk_user->last_day_t / 1073741824, 2)}</code> GB
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="historical_order" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">历史订单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {if $orders->count() !== 0}
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>状态</th>
                                            <th>订单号</th>
                                            <th>商品</th>
                                            <th>商品售价</th>
                                            <th>订单金额</th>
                                            <th>支付方式</th>
                                            <th>创建时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach $orders as $order}
                                            <tr>
                                                <td>{$order->judgmentOrderStatus($order->order_status, $order->expired_at, true)}
                                                </td>
                                                <td>{$order->no}</td>
                                                <td>{$order->product_name}</td>
                                                <td>{sprintf("%.2f", $order->product_price / 100)}</td>
                                                <td>{sprintf("%.2f", $order->order_price / 100)}</td>
                                                <td>{$order->order_payment}</td>
                                                <td>{date('Y-m-d H:i:s', $order->created_at)}</td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    {else}
                        <p>此账户下没有账单</p>
                    {/if}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">确认</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="add-reply" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">添加回复</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea id="reply-content" class="form-control" rows="12" placeholder="请输入回复内容"></textarea>
                    </div>
                    {if $config['quick_fill_function'] === true}
                        <div class="row g-2 align-items-center">
                            {foreach $config['quick_fill_content'] as $item}
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto py-3">
                                    <button id="{$item['id']}" class="btn btn-blue w-100">
                                        {$item['title']}
                                    </button>
                                </div>
                            {/foreach}
                        </div>
                    {/if}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="reply" type="button" class="btn btn-primary" data-bs-dismiss="modal">回复</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="close_ticket_confirm_dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">关闭工单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p>
                            关闭工单后，用户无法继续回复。此工单将归档
                        <p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="confirm_close" type="button" class="btn btn-primary" data-bs-dismiss="modal">确认</button>
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
        $("#reply").click(function() {
            $.ajax({
                url: "/admin/ticket/{$topic->tk_id}",
                type: 'PUT',
                dataType: "json",
                data: {
                    content: $('#reply-content').val()
                },
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

        $("#confirm_close").click(function() {
            $.ajax({
                url: "/admin/ticket/{$topic->tk_id}/close",
                type: 'PUT',
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

        {foreach $config['quick_fill_content'] as $item}
            $("#{$item['id']}").click(function() {
                $("#reply-content").text("{$item['content']}");
            });
        {/foreach}

        $("#success-confirm").click(function() {
            location.reload();
        });
    </script>
{include file='admin/tabler_admin_footer.tpl'}