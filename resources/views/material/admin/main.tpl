<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <meta name="theme-color" content="#4285f4">
    <title>{$config['appName']}</title>
    <!-- css -->
    <link href="/theme/material/css/base.min.css" rel="stylesheet">
    <link href="/theme/material/css/project.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
    <link href="/theme/material/css/material.min.css" rel="stylesheet">
    <link href="/theme/material/css/dataTables.material.min.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/jsoneditor/9.5.8/jsoneditor.min.css" rel="stylesheet" type="text/css">
    <!-- js -->
    <script src="/theme/material/js/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/jsoneditor/9.5.8/jsoneditor.min.js"></script>
    <style>
        body {
            position: relative;
        }

        {if $config['admin_center_bg'] == true}
            .page-brand .ui-content-header {
                background-image: url({$config['admin_center_bg_addr']});
            }

        {/if}

        .table-responsive {
            background: white;
        }

        .dropdown-menu.dropdown-menu-right a {
            color: #212121;
        }

        a[href='#ui_menu'] {
            color: #212121;
        }

        #custom_config {
            height: 500px;
        }
    </style>
</head>

<body class="page-brand">
    <header class="header header-red header-transparent header-waterfall ui-header">
        <ul class="nav nav-list pull-left">
            <div>
                <a data-toggle="menu" href="#ui_menu">
                    <span class="icon icon-lg">menu</span>
                </a>
            </div>
        </ul>
        <ul class="nav nav-list pull-right">
            <div class="dropdown margin-right">
                <a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
                    <span class="access-hide">
                        {$user->user_name}
                    </span>
                    <span class="avatar avatar-sm">
                        <img src="{$user->gravatar}">
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a class="waves-attach" href="/user/">
                            <span class="icon icon-lg margin-right">account_box</span>
                            ????????????
                        </a>
                    </li>
                    <li>
                        <a class="waves-attach" href="/user/logout">
                            <span class="icon icon-lg margin-right">exit_to_app</span>
                            ??????
                        </a>
                    </li>
                </ul>
            </div>
        </ul>
    </header>
    <nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
        <div class="menu-scroll">
            <div class="menu-content">
                <a class="menu-logo" href="/"><i class="icon icon-lg">person_pin</i>&nbsp;????????????</a>
                <ul class="nav">
                    <li>
                        <a class="waves-attach" data-toggle="collapse" href="#ui_menu_me">??????</a>
                        <ul class="menu-collapse collapse in" id="ui_menu_me">
                            <li>
                                <a href="/admin"><i class="icon icon-lg">business_center</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/announcement"><i class="icon icon-lg">announcement</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/ticket"><i class="icon icon-lg">question_answer</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                        </ul>
                        <a class="waves-attach" data-toggle="collapse" href="#ui_menu_config">
                            ??????
                        </a>
                        <ul class="menu-collapse collapse in" id="ui_menu_config">
                            <li>
                                <a href="/admin/setting"><i class="icon icon-lg">settings</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/user"><i class="icon icon-lg">supervisor_account</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/node"><i class="icon icon-lg">router</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                        </ul>
                        <a class="waves-attach" data-toggle="collapse" href="#ui_menu_user">
                            ??????
                        </a>
                        <ul class="menu-collapse collapse in" id="ui_menu_user">
                            <li>
                                <a href="/admin/payback"><i class="icon icon-lg">loyalty</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/subscribe"><i class="icon icon-lg">dialer_sip</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/login"><i class="icon icon-lg">text_fields</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/alive"><i class="icon icon-lg">important_devices</i>&nbsp;
                                    ??????IP
                                </a>
                            </li>
                        </ul>
                        <a class="waves-attach" data-toggle="collapse" href="#ui_menu_trade">??????</a>
                        <ul class="menu-collapse collapse in" id="ui_menu_trade">
                            <li>
                                <a href="/admin/product"><i class="icon icon-lg">shop</i>&nbsp;
                                    ??????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/giftcard"><i class="icon icon-lg">code</i>&nbsp;
                                    ?????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/coupon"><i class="icon icon-lg">card_giftcard</i>&nbsp;
                                    ?????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/order"><i class="icon icon-lg">shopping_cart</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                        </ul>
                        <a class="waves-attach" data-toggle="collapse" href="#ui_menu_detect">
                            ??????
                        </a>
                        <ul class="menu-collapse collapse in" id="ui_menu_detect">
                            <li>
                                <a href="/admin/detect"><i class="icon icon-lg">account_balance</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                            <li>
                                <a href="/admin/detect/log"><i class="icon icon-lg">assignment_late</i>&nbsp;
                                    ????????????
                                </a>
                            </li>
                        </ul>
                    <li>
                        <a href="/user">
                            <i class="icon icon-lg">person</i>&nbsp;
                            ????????????
                        </a>
                    </li>
                </ul>
            </div>
        </div>
</nav>