{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">环境检查</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">检查站点的运行环境与情况</span>
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
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>检测项目</th>
                                        <th>状态</th>
                                        <th>说明</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $alls as $single}
                                        <tr>
                                            <td>{$count++}</td>
                                            <td>{$single['item']}</td>
                                            <td>{$single['status']}</td>
                                            <td style="word-wrap:break-word; word-break:break-all;">{$single['description']}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("td:contains('警告')").css("color", "red");
        $("td:contains('注意')").css("color", "orange");
        $("td:contains('通过')").css("color", "green");
    </script>
{include file='admin/tabler_admin_footer.tpl'}