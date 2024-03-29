<?php

namespace App\Services\Gateway;

use App\Controllers\UserController;

class Epay
{
    public static function _name(): string
    {
        return 'epay';
    }

    public static function _enable(): bool
    {
        if (!isset($_ENV['active_payments']['epay']) || $_ENV['active_payments']['epay']['enable'] === false) {
            return false;
        }

        return true;
    }

    public static function createOrder($amount, $order_no, $user_id)
    {
        $configs = $_ENV['active_payments']['epay'];

        try {
            $params = [
                'money' => $amount,
                'name' => $order_no,
                'pid' => $configs['uid'],
                'notify_url' => $_ENV['baseUrl'] . '/payments/notify/epay',
                'return_url' => $_ENV['baseUrl'] . '/user/order/' . $order_no,
                'out_trade_no' => $order_no,
            ];

            ksort($params);
            reset($params);

            $str = stripslashes(urldecode(http_build_query($params))) . $configs['key'];
            $params['sign'] = md5($str);
            $params['sign_type'] = 'MD5';

            return json_encode([
                'ret' => 1,
                'type' => 'link',
                'link' => $configs['gateway'] . '/submit.php?' . http_build_query($params),
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function notify($request, $response, $args)
    {
        $params = [
            'pid' => $request->getParam('pid'),
            'money' => $request->getParam('money'),
            'trade_status' => $request->getParam('trade_status'),
            'type' => $request->getParam('type'),
            'out_trade_no' => $request->getParam('out_trade_no'),
            'trade_no' => $request->getParam('trade_no'),
        ];

        ksort($params);
        reset($params);

        $sign = $request->getParam('sign');
        $configs = $_ENV['active_payments']['epay'];
        $str = stripslashes(urldecode(http_build_query($params))) . $configs['key'];

        if ($sign !== md5($str)) {
            die('fail');
        }

        UserController::execute($params['out_trade_no']);
        die('success');
    }
}
