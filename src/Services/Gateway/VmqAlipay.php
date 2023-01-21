<?php

namespace App\Services\Gateway;

use App\Controllers\UserController;

class VmqAlipay
{
    public static function _name(): string
    {
        return 'vmq_alipay';
    }

    public static function _enable(): bool
    {
        if (!isset($_ENV['active_payments']['vmq_alipay']) || $_ENV['active_payments']['vmq_alipay']['enable'] === false) {
            return false;
        }

        return true;
    }

    public static function createOrder($amount, $order_no, $user_id)
    {
        $configs = $_ENV['active_payments']['vmq_alipay'];

        try {
            $sign = md5($order_no . $user_id . '2' . $amount . $configs['key']);
            $params = [
                'payId' => $order_no,
                'type' => '2', // 1: wechat; 2: alipay
                'price' => $amount,
                'sign' => $sign,
                'param' => $user_id,
                'isHtml' => '1',
                'notifyUrl' => $_ENV['baseUrl'] . '/payments/notify/vmq_alipay',
                'returnUrl' => $_ENV['baseUrl'] . '/user/order/' . $order_no,
            ];

            return json_encode([
                'ret' => 1,
                'type' => 'link',
                'link' => $configs['gateway'] . '/createOrder?' . http_build_query($params),
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
        $configs = $_ENV['active_payments']['vmq_alipay'];
        $payId = $request->getParam('payId');
        $param = $request->getParam('param');
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        $reallyPrice = $request->getParam('reallyPrice');
        $cloud_sign = $request->getParam('sign');

        $local_sign = md5($payId . $param . $type . $price . $reallyPrice . $configs['key']);

        if ($cloud_sign !== $local_sign) {
            die('fail');
        }

        UserController::execute($payId);
        die('success');
    }
}
