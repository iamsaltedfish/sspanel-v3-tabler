{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">推送任务</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">创建推送任务</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label class="form-label">任务编码</label>
                                    <input type="text" class="form-control" id="task_coding" value="{$task_coding}"
                                        disabled />
                                </div>
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <div class="form-label">邮件类别</div>
                                    <select class="form-select" id="mail_category">
                                        <option value="general_notice">一般公告</option>
                                        <option value="important_notice">重要公告</option>
                                        <option value="market">营销</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <!-- <label class="form-label">推送标题</label> -->
                                <input type="text" class="form-control" id="push_title" placeholder="请输入推送标题" />
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" id="push_content" rows="8"
                                    placeholder="请输入推送正文，支持 html 语法"></textarea>
                            </div>
                            <!--<div class="hr-text">
                                <span>推送渠道</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Simple selectgroup</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="checkbox" name="name" value="email" class="form-selectgroup-input"
                                            checked="">
                                        <span class="form-selectgroup-label">注册邮箱</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="checkbox" name="name" value="telegram"
                                            class="form-selectgroup-input" disabled />
                                        <span class="form-selectgroup-label">Telegram Bot (开发中)</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="checkbox" name="name" value="sms" class="form-selectgroup-input"
                                            disabled />
                                        <span class="form-selectgroup-label">短信 (开发中)</span>
                                    </label>
                                </div>
                            </div>-->
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="hr-text">
                                        <span>预设筛选</span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-label">接收群体</div>
                                        <select class="form-select" id="receiving_group">
                                            {foreach $default_group as $key => $value}
                                                <option value="{$key}">{$value['display_name']}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">变量 x</label>
                                        <input type="text" class="form-control" id="variable_x"
                                            placeholder="预设筛选条件中变量 x 的值">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">变量 y</label>
                                        <input type="text" class="form-control" id="variable_y"
                                            placeholder="预设筛选条件中变量 y 的值">
                                    </div>
                                    <div class="card bg-primary-lt">
                                        <div class="card-body">
                                            <p class="text-muted">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue"
                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                    <polyline points="11 12 12 12 12 16 13 16"></polyline>
                                                </svg>
                                                若筛选条件涉及时间，参数 x, y 的值的格式为：<code>2023-01-01 00:00:00</code>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="hr-text">
                                        <span>自定义筛选</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="row">
                                            <span class="col">使用自定义筛选</span>
                                            <span class="col-auto">
                                                <label class="form-check form-check-single form-switch">
                                                    <input id="custom_filtering" class="form-check-input"
                                                        type="checkbox">
                                                </label>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" id="customize_filtering_conditions" rows="11"
                                            placeholder="自定义筛选条件">[
    [
        "id",
        ">=",
        "100"
    ],
    [
        "id",
        "<=",
        "200"
    ]
]</textarea>
                                    </div>

                                </div>
                            </div>
                            <div class="hr-text">
                                <span>操作</span>
                            </div>
                            <div class="row">
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto py-3">
                                    <button id="preview_effect" class="btn btn-tabler w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-eye-check" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path
                                                d="M12 19c-4 0 -7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7c-.42 .736 -.858 1.414 -1.311 2.033">
                                            </path>
                                            <path d="M15 19l2 2l4 -4"></path>
                                        </svg>
                                        预览邮件推送效果
                                    </button>
                                </div>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto py-3">
                                    <button id="statistical_filter_results" class="btn btn-tabler w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-analyze" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M20 11a8.1 8.1 0 0 0 -6.986 -6.918a8.095 8.095 0 0 0 -8.019 3.918">
                                            </path>
                                            <path d="M4 13a8.1 8.1 0 0 0 15 3"></path>
                                            <path d="M19 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                            <path d="M5 8m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                            <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                        </svg>
                                        统计筛选结果
                                    </button>
                                </div>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto py-3">
                                    <button id="submit_push_task" class="btn btn-tabler w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-circle-plus" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M9 12l6 0"></path>
                                            <path d="M12 9l0 6"></path>
                                        </svg>
                                        提交推送任务
                                    </button>
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

    <div class="modal modal-blur fade" id="notice-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-yellow"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-yellow icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="12" cy="12" r="9"></circle>
                        <line x1="12" y1="17" x2="12" y2="17.01"></line>
                        <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"></path>
                    </svg>
                    <p id="notice-message" class="text-muted">注意</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="notice-confirm" type="button" class="btn btn-yellow" data-bs-dismiss="modal">确认</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#customize_filtering_conditions').hide();

        $('#custom_filtering').click(function() {
            var switch_status = $("#custom_filtering").is(":checked");
            if (switch_status === true) {
                $('#customize_filtering_conditions').show();
            } else {
                $('#customize_filtering_conditions').hide();
            }
        });

        $('#preview_effect').click(function() {
            var title = $('#push_title').val();
            var content = $('#push_content').val();
            // https://blog.csdn.net/renzhenhuai/article/details/19569593
            var mail_category = $('#mail_category option:selected').text();
            var open_url = '/admin/mail/push/preview?title=' + title + '&content=' + content +
                '&mail_category=' + mail_category;
            window.open(open_url, '_blank');
        });

        $("#statistical_filter_results").click(function() {
            $.ajax({
                type: 'POST',
                url: '/admin/mail/push/filter',
                dataType: 'json',
                data: {
                    variable_x: $('#variable_x').val(),
                    variable_y: $('#variable_y').val(),
                    receiving_group: $('#receiving_group').val(),
                    custom_filtering: $("#custom_filtering").is(":checked"),
                    customize_filtering_conditions: $('#customize_filtering_conditions').val(),
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

        $("#submit_push_task").click(function() {
            $('#notice-message').text('确定要提交任务么，此操作无法在后台撤销，创建任务可能耗时较久，期间请勿重复提交');
            $('#notice-dialog').modal('show');
        });

        $("#notice-confirm").click(function() {
            $.ajax({
                type: 'POST',
                url: '/admin/mail/push/progress',
                dataType: 'json',
                data: {
                    variable_x: $('#variable_x').val(),
                    variable_y: $('#variable_y').val(),
                    push_title: $('#push_title').val(),
                    push_content: $('#push_content').val(),
                    task_coding: $('#task_coding').val(),
                    receiving_group: $('#receiving_group').val(),
                    mail_category: $('#mail_category').val(),
                    mail_category_text: $('#mail_category option:selected').text(),
                    custom_filtering: $("#custom_filtering").is(":checked"),
                    customize_filtering_conditions: $('#customize_filtering_conditions').val(),
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                        setTimeout("window.location.href = '/admin/mail/push/progress'", 1500);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });
    </script>

{include file='admin/tabler_admin_footer.tpl'}