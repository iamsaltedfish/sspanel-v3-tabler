{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">配置文件</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">浏览系统配置文件的设定（隐藏了部分敏感参数）</span>
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
                                        <th>项</th>
                                        <th>类型</th>
                                        <th>值</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $alls as $single}
                                        <tr>
                                            <td>{$count++}</td>
                                            <td>{$single['key']}</td>
                                            <td>{$single['type']}</td>
                                            <td style="word-wrap:break-word; word-break:break-all;">{$single['value']}</td>
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
        $("td:contains('empty string')").css("font-style", "italic");
        $("td:contains('true')").css("font-style", "italic");
        $("td:contains('false')").css("font-style", "italic");
        $("td:contains('true')").css("color", "green");
        $("td:contains('false')").css("color", "red");
    </script>

{include file='admin/tabler_admin_footer.tpl'}