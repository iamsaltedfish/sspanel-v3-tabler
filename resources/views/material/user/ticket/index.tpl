{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span class="home-title">工单列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">你可以在这里联系管理员获取支持</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#create-ticket">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            创建工单
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#create-ticket">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                {if $tickets->count() !== 0}
                    {foreach $tickets as $ticket}
                        <div class="col-md-4 col-sm-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-stamp">
                                        {$tk_status = $ticket->getTheWorkOrderStatus($ticket->tk_id)}
                                        {if $tk_status !== 'closed'}
                                            <div class="card-stamp-icon bg-yellow">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock"
                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <polyline points="12 7 12 12 15 15"></polyline>
                                                </svg>
                                            </div>
                                        {else}
                                            <div class="card-stamp-icon bg-green">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check"
                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 12l5 5l10 -10"></path>
                                                </svg>
                                            </div>
                                        {/if}
                                    </div>
                                    <h3 class="card-title" style="font-size: 20px;">
                                        #{$ticket->tk_id} {$ticket->title}
                                    </h3>
                                    <!--<p class="mb-3">
                                                <span class="badge bg-red-lt">Waiting</span>
                                            </p> -->
                                    <p class="text-muted text-truncate" style="height: 100px;">
                                        {nl2br($ticket->getTheLatestReply($ticket->tk_id))}
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex">
                                        {if $tk_status === 'closed'}
                                            <span class="status status-grey">已结单</span>
                                        {/if}
                                        {if $tk_status === 'open_wait_user'}
                                            <span class="status status-orange">等您回复</span>
                                        {/if}
                                        {if $tk_status === 'open_wait_admin'}
                                            <span class="status status-green">进行中</span>
                                        {/if}
                                        <a href="/user/ticket/{$ticket->tk_id}/view" class="btn btn-primary ms-auto">浏览</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {else}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">没有任何工单</h3>
                        </div>
                        <div class="card-body">如需帮助，请点击右上角按钮开启新工单</div>
                    </div>
                {/if}
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="create-ticket" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">创建工单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row row-cards mb-3">
                        <div class="col-lg-12">
                            <div class="card bg-primary-lt">
                                <div class="card-body">
                                    <p class="text-muted" style="line-height: 24px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                            <polyline points="11 12 12 12 12 16 13 16"></polyline>
                                        </svg>
                                        {$config['tips_content']}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <select id="ticket-client" class="form-select">
                            <option value="0">请选择工单类型</option>
                            <option value="reward_or_refund">提现或退款</option>
                            <!-- <option value="Windows">Windows</option>
                            <option value="Macos">Macos</option>
                            <option value="Android">Android</option>
                            <option value="IOS">IOS</option>
                            <option value="Route">路由器</option>
                            <option value="Linux">Linux</option> -->
                            <option value="Other">其他</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input id="ticket-title" type="text" class="form-control" placeholder="请输入工单主题">
                    </div>
                    <div class="mb-3">
                        <textarea id="ticket-content" class="form-control" rows="12" placeholder="请输入工单内容"></textarea>
                    </div>
                    <div class="mb-3">
                        <input id="associated-order" type="text" class="form-control" placeholder="退款请填写订单号；提现请填写金额">
                    </div>
                    <div class="mb-3">
                        <input id="receiving-method" type="text" class="form-control"
                            placeholder="请选择接收方式：{$config['refund_method']}">
                    </div>
                    <div class="mb-3">
                        <input id="receiving-account" type="text" class="form-control"
                            placeholder="请输入接收方式账户，如手机号 / 邮箱 / 收款码图片链接">
                    </div>
                    <p>* 若需上传图片，请使用图床。上传完成后，将图片链接附在工单内容中。您可前往
                        <a target="view_window" href="{$config['img_bed_link']}">
                            {$config['img_bed_link']}
                        </a>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="create-ticket-button" type="button" class="btn btn-primary"
                        data-bs-dismiss="modal">创建</button>
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
                                <a id="success-confirm" href="#" class="btn w-100" data-bs-dismiss="modal">
                                    好
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
        $("#create-ticket-button").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/ticket",
                dataType: "json",
                data: {
                    title: $('#ticket-title').val(),
                    content: $('#ticket-content').val(),
                    ticket_client: $('#ticket-client').val(),
                    receiving_method: $('#receiving-method').val(),
                    receiving_account: $('#receiving-account').val(),
                    associated_order: $('#associated-order').val(),
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

        $("#success-confirm").click(function() {
            location.reload();
        });

        $('#ticket-client').on('change', function() {
            var type = $('#ticket-client').val();
            if (type == 'reward_or_refund') {
                $("#ticket-title").val('提现或退款');
                $("#ticket-title").attr('disabled', true);
                $("#ticket-content").hide();
                $("#receiving-method").show();
                $("#receiving-account").show();
                $("#associated-order").show();
            } else {
                $("#ticket-content").show();
                $("#receiving-method").hide();
                $("#receiving-account").hide();
                $("#associated-order").hide();
                $("#ticket-title").val('');
                $("#ticket-title").attr('disabled', false);
            }
        });

        $("#receiving-method").hide();
        $("#receiving-account").hide();
        $("#associated-order").hide();

        $("td:contains('开启中')").css("color", "green");
        $("td:contains('管理员')").css("color", "purple");
        $("td:contains('您')").css("color", "orange");
    </script>

{include file='user/tabler_footer.tpl'}