{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">在线排行</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">
                            用户在线排行
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="space-y-4">
                        {foreach $top as $data}
                            <div id="alive-{$data['user_id']}" class="accordion" role="tablist" aria-multiselectable="true">
                                <div class="accordion-item">
                                    <div class="accordion-header" role="tab">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                            data-bs-target="#alive-list-{$data['user_id']}">
                                            用户编号 {$data['user_id']} 在线日志有 {$data['count']} 条
                                        </button>
                                    </div>
                                    <div id="alive-list-{$data['user_id']}" class="accordion-collapse collapse"
                                        role="tabpanel" data-bs-parent="#alive-{$data['user_id']}">
                                        <div class="accordion-body pt-0">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter card-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>节点名称</th>
                                                            <th>在线地址</th>
                                                            <th>地址信息</th>
                                                            <th>在线时间</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {foreach $data['logs'] as $log}
                                                            <tr>
                                                                <td>{$log['id']}</td>
                                                                <td>{$nodes[$log['nodeid']]}</td>
                                                                <td>{$log['ip']}</td>
                                                                <td>{$ip_location[$log['ip']]}</td>
                                                                <td>{date('Y-m-d H:i:s', $log['datetime'])}</td>
                                                            </tr>
                                                        {/foreach}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
{include file='admin/tabler_admin_footer.tpl'}