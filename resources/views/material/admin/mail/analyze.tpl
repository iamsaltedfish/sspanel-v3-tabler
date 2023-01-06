{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">邮件统计</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">查看近日系统发送的各类型邮件数量</span>
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
                        <div class="card-header">
                            <h3 class="card-title">
                                邮件统计
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="mail-statistics"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            window.ApexCharts && (new ApexCharts(document.getElementById('mail-statistics'), {
                chart: {
                    type: "line",
                    fontFamily: 'inherit',
                    height: 360,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: false
                    },
                },
                fill: {
                    opacity: 1,
                },
                stroke: {
                    width: 2,
                    lineCap: "round",
                    curve: "smooth",
                    //curve: "straight",
                },
                series: {json_encode($chart_content, 320)},
                tooltip: {
                    theme: 'light'
                },
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: -4,
                        bottom: -4
                    },
                    strokeDashArray: 4,
                },
                xaxis: {
                    labels: {
                        padding: 0,
                    },
                    tooltip: {
                        enabled: false
                    },
                    categories: [
                        {implode(', ', $categories)}
                    ],
                },
                yaxis: {
                    labels: {
                        padding: 4
                    },
                },
                legend: {
                    show: false,
                },
            })).render();
        });
        // @formatter:on
    </script>

{include file='admin/tabler_admin_footer.tpl'}