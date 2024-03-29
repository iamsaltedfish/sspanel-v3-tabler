<?php
declare(strict_types=1);

use Slim\App as SlimApp;
use App\Middleware\{Auth, Guest, Admin, WebApiAuth, AuthorizationBearer};

return function (SlimApp $app) {
    // Home
    $app->get('/',          App\Controllers\HomeController::class . ':index');
    $app->get('/404',       App\Controllers\HomeController::class . ':page404');
    $app->get('/405',       App\Controllers\HomeController::class . ':page405');
    $app->get('/500',       App\Controllers\HomeController::class . ':page500');
    $app->get('/tos',       App\Controllers\HomeController::class . ':tos');
    $app->get('/staff',     App\Controllers\HomeController::class . ':staff');

    // Other
    $app->post('/notify',                       App\Controllers\HomeController::class . ':notify');

    // Telegram
    $app->post('/telegramCallback',             App\Controllers\HomeController::class . ':telegram');

    // User Center
    $app->group('/user', function () {
        // 用户中心首页
        $this->get('',                          App\Controllers\UserController::class . ':index');
        $this->get('/',                         App\Controllers\UserController::class . ':index');
        $this->post('/checkin',                 App\Controllers\UserController::class . ':doCheckin');

        // 单页面
        $this->get('/media',                    App\Controllers\UserController::class . ':media');
        $this->get('/profile',                  App\Controllers\UserController::class . ':profile');
        $this->post('/kill',                    App\Controllers\UserController::class . ':handleKill');
        $this->get('/disable',                  App\Controllers\UserController::class . ':disable');
        $this->get('/announcement',             App\Controllers\UserController::class . ':announcement');
        $this->get('/subscribe_log',            App\Controllers\UserController::class . ':subscribeLog');

        // 文档中心
        $this->get('/docs/{client}',            App\Controllers\DocsController::class . ':index');
        $this->get('/faq',                      App\Controllers\DocsController::class . ':faqIndex');

        // 邀请系统
        $this->get('/invite',                   App\Controllers\UserController::class . ':invite');
        $this->put('/invite',                   App\Controllers\UserController::class . ':resetInviteURL');

        // 审计系统
        if (!$_ENV['hide_audit_rules_and_logs']) {
            $this->get('/detect',               App\Controllers\UserController::class . ':detectIndex');
            $this->get('/detect/log',           App\Controllers\UserController::class . ':detectLog');
        }

        // 工单系统
        $this->get('/ticket',                   App\Controllers\User\TicketController::class . ':ticket');
        $this->get('/ticket/create',            App\Controllers\User\TicketController::class . ':ticketCreate');
        $this->post('/ticket',                  App\Controllers\User\TicketController::class . ':ticketAdd');
        $this->get('/ticket/{id}/view',         App\Controllers\User\TicketController::class . ':ticketView');
        $this->put('/ticket/{id}',              App\Controllers\User\TicketController::class . ':ticketUpdate');

        // 新商店系统
        $this->get('/product',                  App\Controllers\UserController::class . ':productIndex');
        $this->get('/order',                    App\Controllers\UserController::class . ':orderIndex');
        $this->get('/order/{no}',               App\Controllers\UserController::class . ':orderDetails');
        $this->get('/order/status/{no}',        App\Controllers\UserController::class . ':orderStatus');
        $this->post('/order',                   App\Controllers\UserController::class . ':createOrder');
        $this->put('/order',                    App\Controllers\UserController::class . ':processOrder');
        $this->post('/redeem',                  App\Controllers\UserController::class . ':redeemGiftCard');
        $this->post('/coupon_check',            App\Controllers\UserController::class . ':couponCheck');
        $this->post('/charge',                  App\Controllers\UserController::class . ':balanceCharge');

        // 编辑页面
        $this->get('/edit',                     App\Controllers\UserController::class . ':edit');
        $this->get('/telegram_reset',           App\Controllers\UserController::class . ':telegramReset');
        $this->post('/email',                   App\Controllers\UserController::class . ':updateEmail');
        $this->post('/reminder',                App\Controllers\UserController::class . ':updateReminder');
        $this->post('/username',                App\Controllers\UserController::class . ':updateUsername');
        $this->post('/password',                App\Controllers\UserController::class . ':updatePassword');
        $this->post('/send',                    App\Controllers\AuthController::class . ':sendVerify');
        $this->post('/wechat',                  App\Controllers\UserController::class . ':updateWechat');
        $this->post('/ssr',                     App\Controllers\UserController::class . ':updateSSR');
        $this->post('/theme',                   App\Controllers\UserController::class . ':updateTheme');
        $this->post('/sspwd',                   App\Controllers\UserController::class . ':updateSsPwd');
        $this->post('/url_reset',               App\Controllers\UserController::class . ':resetURL');
        $this->post('/gacheck',                 App\Controllers\UserController::class . ':gaCheck');
        $this->post('/gaset',                   App\Controllers\UserController::class . ':gaSet');
        $this->post('/gareset',                 App\Controllers\UserController::class . ':gaReset');
        $this->post('/port',                    App\Controllers\UserController::class . ':resetPort');

        // 节点列表
        $this->get('/server',                   App\Controllers\User\NodeController::class . ':serverList');

        // 其他
        $this->get('/logout',                   App\Controllers\UserController::class . ':logout');
    })->add(new Auth());

    $app->group('/payments', function () {
        $this->get('/notify/{type}',            App\Services\Payment::class . ':notify');
        $this->post('/notify/{type}',           App\Services\Payment::class . ':notify');
    });

    // Auth
    $app->group('/auth', function () {
        $this->get('/login',            App\Controllers\AuthController::class . ':login');
        $this->post('/login',           App\Controllers\AuthController::class . ':loginHandle');
        $this->get('/register',         App\Controllers\AuthController::class . ':register');
        $this->post('/register',        App\Controllers\AuthController::class . ':registerHandle');
        $this->post('/send',            App\Controllers\AuthController::class . ':sendVerify');
        $this->get('/logout',           App\Controllers\AuthController::class . ':logout');
    })->add(new Guest());

    // Password
    $app->group('/password', function () {
        $this->get('/reset',            App\Controllers\PasswordController::class . ':reset');
        $this->post('/reset',           App\Controllers\PasswordController::class . ':handleReset');
        $this->get('/token/{token}',    App\Controllers\PasswordController::class . ':token');
        $this->post('/token/{token}',   App\Controllers\PasswordController::class . ':handleToken');
    })->add(new Guest());

    // Admin
    $app->group('/admin', function () {
        $this->get('',                          App\Controllers\AdminController::class . ':index');
        $this->get('/',                         App\Controllers\AdminController::class . ':index');

        $this->get('/payback',                  App\Controllers\Admin\PaybackController::class . ':index');
        $this->post('/payback/ajax',            App\Controllers\Admin\PaybackController::class . ':ajaxQuery');
        $this->put('/payback/{id}',             App\Controllers\Admin\PaybackController::class . ':amendmentReward');
        $this->delete('/payback/{id}',          App\Controllers\Admin\PaybackController::class . ':delete');

        // Node Mange
        $this->get('/node',                     App\Controllers\Admin\NodeController::class . ':index');
        $this->get('/node/create',              App\Controllers\Admin\NodeController::class . ':create');
        $this->post('/node',                    App\Controllers\Admin\NodeController::class . ':add');
        $this->post('/node/copy',               App\Controllers\Admin\NodeController::class . ':copy');
        $this->get('/node/{id}/edit',           App\Controllers\Admin\NodeController::class . ':edit');
        $this->put('/node/{id}',                App\Controllers\Admin\NodeController::class . ':update');
        $this->delete('/node',                  App\Controllers\Admin\NodeController::class . ':delete');
        $this->post('/node/ajax',               App\Controllers\Admin\NodeController::class . ':ajax');

        // Ticket Mange
        $this->get('/ticket',                   App\Controllers\Admin\TicketController::class . ':index');
        $this->get('/ticket/{id}/view',         App\Controllers\Admin\TicketController::class . ':read');
        $this->put('/ticket/{id}',              App\Controllers\Admin\TicketController::class . ':addReply');
        $this->put('/ticket/{id}/close',        App\Controllers\Admin\TicketController::class . ':closeTk');
        $this->post('/ticket/ajax',             App\Controllers\Admin\TicketController::class . ':ajaxQuery');
        $this->delete('/ticket/{id}',           App\Controllers\Admin\TicketController::class . ':delete');

        // Product Mange
        $this->get('/product',                  App\Controllers\Admin\ProductController::class . ':index');
        $this->get('/product/details/{id}',     App\Controllers\Admin\ProductController::class . ':get');
        $this->post('/product',                 App\Controllers\Admin\ProductController::class . ':save');
        $this->put('/product/{id}',             App\Controllers\Admin\ProductController::class . ':update');
        $this->delete('/product/{id}',          App\Controllers\Admin\ProductController::class . ':delete');

        // Order Mange
        $this->get('/order',                    App\Controllers\Admin\OrderController::class . ':index');
        $this->get('/order/refund/{no}',        App\Controllers\Admin\OrderController::class . ':refundPreview');
        $this->post('/order/refund/{no}',       App\Controllers\Admin\OrderController::class . ':refundExecution');
        $this->post('/order/ajax',              App\Controllers\Admin\OrderController::class . ':ajaxQuery');

        // Gift Card
        $this->get('/giftcard',                 App\Controllers\Admin\GiftCardController::class . ':index');
        $this->post('/giftcard',                App\Controllers\Admin\GiftCardController::class . ':add');
        $this->post('/giftcard/ajax',           App\Controllers\Admin\GiftCardController::class . ':ajaxQuery');
        $this->delete('/giftcard/{id}',         App\Controllers\Admin\GiftCardController::class . ':delete');

        // Log Mange
        $this->get('/log',                      App\Controllers\Admin\LogController::class . ':index');
        $this->post('/log/ajax',                App\Controllers\Admin\LogController::class . ':ajaxQuery');
        $this->put('/log/{id}',                 App\Controllers\Admin\LogController::class . ':update');
        $this->delete('/log/{id}',              App\Controllers\Admin\LogController::class . ':delete');

        // Mail Mange
        $this->get('/mail/log',                 App\Controllers\Admin\MailController::class . ':index');
        $this->post('/mail/log/ajax',           App\Controllers\Admin\MailController::class . ':ajaxQuery');
        $this->get('/mail/push/task',           App\Controllers\Admin\MailController::class . ':task');
        $this->get('/mail/push/preview',        App\Controllers\Admin\MailController::class . ':preview');
        $this->post('/mail/push/filter',        App\Controllers\Admin\MailController::class . ':filter');
        $this->get('/mail/push/progress',       App\Controllers\Admin\MailController::class . ':progressList');
        $this->post('/mail/push/progress',      App\Controllers\Admin\MailController::class . ':progress');

        // Mail Block Mange
        $this->get('/mail/block',               App\Controllers\Admin\MailBlockController::class . ':index');
        $this->post('/mail/block/ajax',         App\Controllers\Admin\MailBlockController::class . ':ajaxQuery');
        $this->put('/mail/block',               App\Controllers\Admin\MailBlockController::class . ':ajaxUpdate');

        // Mail Analyze Mange
        $this->get('/mail/analyze',             App\Controllers\Admin\MailAnalyzeController::class . ':index');

        // Ann Mange
        $this->get('/announcement',             App\Controllers\Admin\AnnController::class . ':index');
        $this->get('/announcement/create',      App\Controllers\Admin\AnnController::class . ':create');
        $this->post('/announcement',            App\Controllers\Admin\AnnController::class . ':add');
        $this->get('/announcement/{id}/edit',   App\Controllers\Admin\AnnController::class . ':edit');
        $this->put('/announcement/{id}',        App\Controllers\Admin\AnnController::class . ':update');
        $this->delete('/announcement',          App\Controllers\Admin\AnnController::class . ':delete');
        $this->post('/announcement/ajax',       App\Controllers\Admin\AnnController::class . ':ajax');

        // Top Mange
        $this->get('/chart/index',              App\Controllers\Admin\ChartController::class . ':index');
        $this->get('/chart/finance',            App\Controllers\Admin\ChartController::class . ':finance');
        $this->get('/chart/user/{date}',        App\Controllers\Admin\ChartController::class . ':user');
        $this->get('/chart/node/{date}',        App\Controllers\Admin\ChartController::class . ':node');

        // Detect Mange
        $this->get('/detect',                   App\Controllers\Admin\DetectController::class . ':index');
        $this->get('/detect/create',            App\Controllers\Admin\DetectController::class . ':create');
        $this->post('/detect',                  App\Controllers\Admin\DetectController::class . ':add');
        $this->get('/detect/{id}/edit',         App\Controllers\Admin\DetectController::class . ':edit');
        $this->put('/detect/{id}',              App\Controllers\Admin\DetectController::class . ':update');
        $this->delete('/detect',                App\Controllers\Admin\DetectController::class . ':delete');
        $this->get('/detect/log',               App\Controllers\Admin\DetectController::class . ':log');
        $this->post('/detect/ajax',             App\Controllers\Admin\DetectController::class . ':ajaxRule');
        $this->post('/detect/log/ajax',         App\Controllers\Admin\DetectController::class . ':ajaxLog');

        // IP Mange
        $this->get('/login',                    App\Controllers\Admin\IpController::class . ':index');
        $this->get('/alive',                    App\Controllers\Admin\IpController::class . ':alive');
        $this->get('/alive/top',                App\Controllers\Admin\IpController::class . ':aliveTop');
        $this->post('/login/ajax',              App\Controllers\Admin\IpController::class . ':ajaxQuery');
        $this->post('/alive/ajax',              App\Controllers\Admin\IpController::class . ':ajaxAlive');

        // User Mange
        //$this->get('/user',                   App\Controllers\Admin\UserController::class . ':index');
        $this->get('/user/{id}/edit',           App\Controllers\Admin\UserController::class . ':edit');
        $this->put('/user/{id}',                App\Controllers\Admin\UserController::class . ':update');
        //$this->delete('/user',                App\Controllers\Admin\UserController::class . ':delete');
        //$this->post('/user/changetouser',     App\Controllers\Admin\UserController::class . ':changetouser');
        //$this->post('/user/ajax',             App\Controllers\Admin\UserController::class . ':ajax');
        $this->post('/user/create',             App\Controllers\Admin\UserController::class . ':createNewUser');
        $this->get('/user',                     App\Controllers\Admin\UserController::class . ':index');
        $this->post('/user/ajax',               App\Controllers\Admin\UserController::class . ':ajaxQuery');
        $this->delete('/user/{id}',             App\Controllers\Admin\UserController::class . ':delete');

        // Coupon Mange
        $this->get('/coupon',                   App\Controllers\Admin\CouponController::class . ':index');
        $this->get('/coupon/details/{id}',      App\Controllers\Admin\CouponController::class . ':get');
        $this->post('/coupon',                  App\Controllers\Admin\CouponController::class . ':save');
        $this->put('/coupon/{id}',              App\Controllers\Admin\CouponController::class . ':update');
        $this->delete('/coupon/{id}',           App\Controllers\Admin\CouponController::class . ':delete');

        // Subscribe Log Mange
        $this->get('/subscribe',                App\Controllers\Admin\SubscribeLogController::class . ':index');
        $this->post('/subscribe/ajax',          App\Controllers\Admin\SubscribeLogController::class . ':subscribeAjax');

        // 设置中心
        $this->get('/setting',                  App\Controllers\Admin\SettingController::class . ':index');
        $this->post('/setting',                 App\Controllers\Admin\SettingController::class . ':save');
        $this->post('/setting/email',           App\Controllers\Admin\SettingController::class . ':test');

        // 配置文件
        $this->get('/config',                   App\Controllers\Admin\ConfigController::class . ':index');
        $this->get('/check',                    App\Controllers\Admin\CheckController::class . ':index');
    })->add(new Admin());

    if ($_ENV['enableAdminApi']){
        $app->group('/admin/api', function () {
            // e.g curl -H "Authorization: BEARER 7cb4ddeaea0a1a7a42f351f71a28124a" https://domain.com/admin/api/nodes | jq .
            $this->get('/ping',              App\Controllers\Admin\ApiController::class . ':ping');
            $this->get('/nodes',             App\Controllers\Admin\ApiController::class . ':getNodeList');
            $this->get('/node/{id}',         App\Controllers\Admin\ApiController::class . ':getNodeInfo');
            $this->get('/nodes/relay',       App\Controllers\Admin\ApiController::class . ':getNodeRelayList');
            $this->get('/nodeid/{ip}',       App\Controllers\Admin\ApiController::class . ':getNodeId');
            $this->put('/node/{id}/port',    App\Controllers\Admin\ApiController::class . ':changeServicePort');
            $this->get('/node/{id}/status',  App\Controllers\Admin\ApiController::class . ':getNodeStatus');

            // Re-bind controller, bypass admin token require
            $this->post('/node',             App\Controllers\Admin\NodeController::class . ':add');
            $this->put('/node/{id}',         App\Controllers\Admin\NodeController::class . ':update');
            $this->delete('/node',           App\Controllers\Admin\NodeController::class . ':delete');
        })->add(new AuthorizationBearer($_ENV['adminApiToken']));
    }

    // mu
    $app->group('/mod_mu', function () {
        // 流媒体检测
        $this->post('/media/saveReport',    App\Controllers\WebApi\NodeController::class . ':saveReport');
        // 其他
        $this->get('/nodes',                App\Controllers\WebApi\NodeController::class . ':getAllInfo');
        $this->get('/nodes/{id}/info',      App\Controllers\WebApi\NodeController::class . ':getInfo');
        $this->post('/nodes/{id}/info',     App\Controllers\WebApi\NodeController::class . ':info');

        $this->get('/users',                App\Controllers\WebApi\UserController::class . ':index');
        $this->get('/users/traffic',        App\Controllers\WebApi\UserController::class . ':getTraffic');
        $this->post('/users/traffic',       App\Controllers\WebApi\UserController::class . ':addTraffic');
        $this->post('/users/aliveip',       App\Controllers\WebApi\UserController::class . ':addAliveIp');
        $this->post('/users/detectlog',     App\Controllers\WebApi\UserController::class . ':addDetectLog');

        $this->get('/func/detect_rules',    App\Controllers\WebApi\FuncController::class . ':getDetectLogs');
        $this->post('/func/block_ip',       App\Controllers\WebApi\FuncController::class . ':addBlockIp');
        $this->get('/func/block_ip',        App\Controllers\WebApi\FuncController::class . ':getBlockip');
        $this->get('/func/unblock_ip',      App\Controllers\WebApi\FuncController::class . ':getUnblockip');
        $this->get('/func/ping',            App\Controllers\WebApi\FuncController::class . ':ping');
        $this->post('/func/log',            App\Controllers\WebApi\FuncController::class . ':log');
        // e.g curl -X POST https:///domain.com/mod_mu/func/log?key=123456 -d 'type=1&reporter=2&level=low&msg=4'
    })->add(new WebApiAuth());

    $app->group('/link', function () {
        $this->get('/{token}',              App\Controllers\NewLinkController::class . ':requestEntry');
    });

    $app->group('/mail', function () {
        $this->get('/push/{token}',         App\Controllers\PushController::class . ':index');
        $this->post('/push/{token}',        App\Controllers\PushController::class . ':update');
    });

    //通用訂閲
    $app->group('/sub', function () {
        $this->get('/{token}/{subtype}',    App\Controllers\SubController::class . ':getContent');
    });
};
