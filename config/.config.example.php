<?php

/*
    网站设置
*/

$_ENV['debug'] = false; // 生产环境需设为false
$_ENV['appName'] = 'sspanel-uim'; // 站点名称
$_ENV['key'] = '32150285b345c48aa3492f9212f61ca2'; // 修改为随机字符串
$_ENV['baseUrl'] = 'https://domain.com';// 站点地址
$_ENV['mail_baseUrl'] = 'https://domain.com';// 邮件内使用的地址

/*
    数据库设置
*/

// db_host|db_socket 二选一，若设置 db_socket 则 db_host 会被忽略，不用请留空。若数据库在本机上推荐用 db_socket
// db_host 例: localhost(可解析的主机名), 127.0.0.1(IP 地址), 10.0.0.2:4406(含端口)
// db_socket 例：/var/run/mysqld/mysqld.sock(需使用绝对地址)

$_ENV['db_host'] = 'localhost';
$_ENV['db_database'] = '';
$_ENV['db_username'] = '';
$_ENV['db_password'] = '';

$_ENV['db_socket'] = '';
$_ENV['db_prefix'] = '';
$_ENV['db_driver'] = 'mysql';
$_ENV['db_charset'] = 'utf8mb4';
$_ENV['db_collation'] = 'utf8mb4_unicode_ci';

/*
    支付设置
*/

$_ENV['active_payments'] = [
    'alipay_f2f' => [
        'name' => '支付宝',
        'rate' => '0.0038', // 网关费率
        'min' => '10',
        'max' => '1000',
        'visible_range' => false, // 限制支付方式的可见范围
        'visible_min_range' => '', // 此支付方式可见最小用户id（含）
        'visible_max_range' => '', // 可见最大用户id（含）
        'enable' => false,
        'f2f_pay_app_id'=> '',
        'f2f_pay_pid'=> '',
        'f2f_pay_public_key'=> '',
        'f2f_pay_private_key'=> '',
    ],
    'universal' => [
        'name' => '',
        'rate' => '0',
        'min' => '10',
        'max' => '1000',
        'visible_range' => false, // 限制支付方式的可见范围
        'visible_min_range' => '', // 此支付方式可见最小用户id（含）
        'visible_max_range' => '', // 可见最大用户id（含）
        'gateway' => '',
        'sign_key' => '',
        'enable' => false,
    ],
    'epay' => [
        'name' => '易支付',
        'rate' => '0',
        'min' => '10',
        'max' => '1000',
        'visible_range' => false, // 限制支付方式的可见范围
        'visible_min_range' => '', // 此支付方式可见最小用户id（含）
        'visible_max_range' => '', // 可见最大用户id（含）
        'gateway' => '',
        'uid' => '',
        'key' => '',
        'enable' => false,
    ],
    'vmq_alipay' => [
        'name' => '支付宝',
        'rate' => '0',
        'min' => '10',
        'max' => '1000',
        'visible_range' => false, // 限制支付方式的可见范围
        'visible_min_range' => '', // 此支付方式可见最小用户id（含）
        'visible_max_range' => '', // 可见最大用户id（含）
        'gateway' => '',
        'key' => '',
        'enable' => false,
    ],
    'vmq_wechat' => [
        'name' => '微信',
        'rate' => '0',
        'min' => '10',
        'max' => '1000',
        'visible_range' => false, // 限制支付方式的可见范围
        'visible_min_range' => '', // 此支付方式可见最小用户id（含）
        'visible_max_range' => '', // 可见最大用户id（含）
        'gateway' => '',
        'key' => '',
        'enable' => false,
    ],
    'well_pay' => [
        'name' => 'WellPay',
        'rate' => '0',
        'min' => '10',
        'max' => '1000',
        'visible_range' => false, // 限制支付方式的可见范围
        'visible_min_range' => '', // 此支付方式可见最小用户id（含）
        'visible_max_range' => '', // 可见最大用户id（含）
        'gateway' => 'https://check.bbapk.xyz/api/Wellpay/create',
        'appid' => '',
        'appkey' => '',
        'enable' => false,
    ],
];

/*
    与 Web Api 有关的设置
*/

$_ENV['WebAPI'] = true;
$_ENV['muKey'] = '3a7caa4b32ffb47e7bb2d0ec7d097110'; // 通信密钥
$_ENV['checkNodeIp'] = true; // 是否验证节点ip

$_ENV['enableAdminApi'] = false; // 是否启用 Admin API, 如果不知道此项用途请保持为 false
$_ENV['adminApiToken'] = '7cb4ddeaea0a1a7a42f351f71a28124a'; // Admin API 的 Token, 请生成为高强度的 Token

// 如下设置将使397，297号节点复用4号节点的流媒体解锁
$_ENV['streaming_media_unlock_multiplexing'] = [
    //'397' => '4',
    //'297' => '4',
];

/*
    工单系统
*/

$_ENV['enable_ticket'] = true; // 是否开启工单系统
$_ENV['user_ticket_mail_notify'] = true; // 用户的工单有新回复时邮件通知其注册邮箱
$_ENV['admin_ticket_mail_notify'] = true; // 用户回复工单时邮件通知所有管理员
$_ENV['admin_ticket_telegram_notify'] = true; // 用户回复工单时推送给所有绑定了telegram的管理员
$_ENV['quick_fill_function'] = false; // 工单回复快速填充内容开关
$_ENV['quick_fill_content'] = [
    [
        'id' => 'how_buy',
        'title' => '如何购买',
        'content' => '您可以前往 /user/product 页面选购商品',
    ],
    [
        'id' => 'how_use',
        'title' => '如何使用',
        'content' => '您可以参考 docs.domain.com 页面的导航',
    ]
];
$_ENV['img_bed_link'] = 'https://imgkr.com/'; // 提供的图床地址
$_ENV['refund_method'] = '支付宝 / 微信 / USDT TRC20'; // 向用户声明支持的方式
$_ENV['tips_content'] = '工单被回复时会邮件通知您，请留意注册邮箱的推送';

/*
    注册用户行为限制
*/

$_ENV['rebate_risk_control'] = false; // 返利风控，开启时会通过比对邀请人和被邀请人在注册和登录时收集的浏览器指纹是否有交集来判断是否欺诈
$_ENV['enable_kill'] = false; // 是否允许用户主动删除账户
$_ENV['enable_change_email'] = false;  // 是否允许用户主动更改账户邮箱
$_ENV['enable_checkin'] = true; // 是否允许用户签到
$_ENV['enable_expired_checkin'] = true; // 是否允许过期用户签到
$_ENV['checkinMin'] = 100; // 签到可获得的最低流量(MB)
$_ENV['checkinMax'] = 300; // 签到可获得的最多流量(MB)
$_ENV['checkin_add_time'] = false; // 签到是否增加时间
$_ENV['checkin_add_time_hour'] = 4; // 签到增加多长小时的时间
$_ENV['gift_card_rebate'] = false; // 当用户兑换礼品卡添加余额时，是否执行返利
$_ENV['public_product_rebate_comparison_table'] = false; // 在邀请注册页面公开商品返利对照表
$_ENV['hide_old_server_list'] = false; // 是否隐藏旧的服务器列表入口
$_ENV['hide_audit_rules_and_logs'] = false; // 是否隐藏审计规则与日志入口
$_ENV['show_live_chat_on_logout_page'] = false; // 是否在未登录页面显示livechat
$_ENV['invisible_livechat_users'] = ['0']; // 不可见livechat的用户id列表

/*
    邀请权限设置
*/

$_ENV['registration_duration_switch'] = false; // 根据注册时长限制邀请权限
$_ENV['registration_duration'] = 90; // 注册多少天才能邀请人
$_ENV['consumption_amount_switch'] = false; // 根据消费金额限制邀请权限
$_ENV['consumption_amount'] = 60; // 消费多少元才能邀请人
// 若 registration_duration_switch 和 consumption_amount_switch 都为真且此项目为真
// 则用户只需满足其中一条规则即有邀请权限
$_ENV['one_of_the_conditions_is_satisfied'] = false;

/*
    页面自定义
*/

$_ENV['page_load_data_entry'] = 1000; // 页面加载数据条目
$_ENV['user_media_page_custom'] = false; // 流媒体解锁页面自定义说明开关
$_ENV['user_media_page_custom_text'] = '<p>Hi</p>'; // 流媒体解锁页面自定义说明文本，支持html
$_ENV['user_product_page_custom'] = false; // 商品页面自定义说明开关
$_ENV['user_product_page_custom_text'] = '<p>Hi</p>'; // 商品页面自定义说明文本，支持html
$_ENV['enable_docs'] = true; // 是否开启文档系统
$_ENV['docs_sub_hidden'] = ['ssa', 'anxray']; // 文档中心首页订阅选项隐藏列表
$_ENV['enable_faq'] = true; // 是否展示使用问答入口
$_ENV['faqs'] = [
    // 问题的分类
    '节点' => [
        [
            'mark' => 'node-1', // 问题的标签，可以随意，但是不能重复
            'is_first' => true, // 是不是该分类下的第一个问答，是填true，不是填false
            'question' => '节点不能用了怎么办？', // 问题
            'answer' => '换其他的', //回答，支持html
        ],
        [
            'mark' => 'node-2',
            'is_first' => false,
            'question' => '哪些节点能看Netflix？',
            'answer' => '参考 <a href="/user/media">流媒体解锁</a> 页面即可',
        ],
    ],
    '收费' => [
        [
            'mark' => 'charge-1',
            'is_first' => true,
            'question' => '支持退款么？',
            'answer' => '支持',
        ],
        [
            'mark' => 'charge-2',
            'is_first' => false,
            'question' => '可以升级套餐么？',
            'answer' => '可以',
        ]
    ]
];
$_ENV['statistics_range'] = [
    'checkin' => 30,
    'traffic' => 30,
    'register' => 30,
    'sale' => 30, // sale, deal_amount, order_amount 需保持一致
    'deal_amount' => 30,
    'order_amount' => 30,
    'mail_count' => 30,
];

/*
    与邮件相关设置
*/

$_ENV['sendPageLimit'] = 50; // 发信分页数
$_ENV['email_queue'] = true; // 邮件队列开关
$_ENV['mail_filter'] = 0; // 0关闭; 1白名单模式; 2黑名单模式
$_ENV['mail_filter_list'] = ['qq.com', 'vip.qq.com', 'foxmail.com'];
$_ENV['mail_push_salt'] = 'c669c8b3277fa0415aaea21d78fccdd7';

/*
    后端设置
*/

$_ENV['keep_connect'] = false; // 流量耗尽则限速1Mbps
$_ENV['disconnect_time'] = 60; // 在用户超过套餐连接IP数后多久才会拒绝新连接

$_ENV['min_port'] = 10000; // 0为不分配; 其他值时为分配起始端口
$_ENV['max_port'] = 60000; // 0为不分配; 其他值时为分配终止端口

/*
    Telegram bot
*/

$_ENV['telegram_bot'] = ''; // 机器人用户名
$_ENV['telegram_token'] = ''; // 机器人token
$_ENV['enable_telegram'] = false; // 机器人开关

/*
    订阅设置
*/

$_ENV['Subscribe'] = true; // 本站是否提供订阅功能
$_ENV['subUrl'] = $_ENV['baseUrl'] . '/link/'; // 订阅地址，如需和站点名称相同，请不要修改
$_ENV['enable_forced_replacement'] = true; // 用户修改账户登录密码时，是否强制更换订阅地址

/*
    订阅日志设置
*/

$_ENV['subscribeLog'] = true; // 是否记录用户订阅日志
$_ENV['subscribeLog_show'] = true; // 是否允许用户查看订阅记录
$_ENV['subscribeLog_keep_days'] = 7; // 订阅记录保留天数

/*
    注册设置
*/

$_ENV['random_group'] = '0'; // 注册时随机分配到的分组，多个用英文半角逗号分隔
$_ENV['enable_reg_im'] = true; // 注册时是否要求用户输入IM联系方式
$_ENV['reg_invite_num'] = 100; // 注册时默认的邀请码可用次数，开放注册模式下不扣减邀请码次数，仅在仅允许邀请注册的情况下扣减
$_ENV['disposable_invite_code'] = false; // 一次性邀请码，开启时，用户的邀请码会在每次使用后随机一个新的
$_ENV['reg_money'] = 0; // 注册时默认的账户余额，可以设置一个数，然后引导用户在商店购买试用套餐
$_ENV['reg_default_traffic'] = 20; // 注册时赠送的流量，单位gb
$_ENV['reg_default_time'] = 24; // 注册时赠送的账户时长，单位hour
$_ENV['reg_default_class'] = 0; // 注册时默认设置的等级
$_ENV['reg_default_class_time'] = 24; // 注册时赠送的等级时长，单位hour
$_ENV['reg_obfs'] = 'plain'; // 注册时默认的混淆
$_ENV['reg_method'] = 'rc4-md5'; // 注册时默认的加密
$_ENV['reg_protocol'] = 'origin'; // 注册时默认的协议
$_ENV['reg_obfs_param'] = 'world.taobao.com'; // 注册时默认的混淆参数
$_ENV['reg_protocol_param'] = ''; // 注册时默认的协议参数

/*
    第三方服务
*/

// sentry.io
$_ENV['sentry_dsn'] = '';

/*
    杂项
*/

$_ENV['authDriver'] = 'cookie'; // 不能更改
$_ENV['pwdMethod'] = 'md5'; // md5, sha256, bcrypt, argon2i, argon2id
$_ENV['salt'] = ''; // 加盐仅支持 md5, sha256
$_ENV['theme'] = 'material'; // 默认主题
$_ENV['timeZone'] = 'PRC'; // PRC / UTC
$_ENV['jump_delay'] = 1200; // 页面跳转延时
$_ENV['enable_login_bind_ip'] = true; // 是否将登陆线程和IP绑定
$_ENV['hidden_transit_server_ip'] = false; // 账户信息页面的最近使用ip是否排除展示下列ip
$_ENV['hidden_transit_server_ip_list'] = ['8.8.8.8']; // 排除展示的ip
$_ENV['marked_site_server_login_ip'] = false; // 账户信息页面的登录ip如果是站点服务器ip则进行标注

/*
    获取客户端地址
*/

$_ENV['cdn_forwarded_ip'] = ['HTTP_X_FORWARDED_FOR', 'HTTP_ALI_CDN_REAL_IP', 'X-Real-IP', 'True-Client-Ip'];
foreach ($_ENV['cdn_forwarded_ip'] as $cdn_forwarded_ip) {
    if (isset($_SERVER[$cdn_forwarded_ip])) {
        $list = explode(',', $_SERVER[$cdn_forwarded_ip]);
        $_SERVER['REMOTE_ADDR'] = $list[0];
        break;
    }
}
