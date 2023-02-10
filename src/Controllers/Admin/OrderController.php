<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Payback;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\User;

class OrderController extends AdminController
{
    public function index($request, $response, $args)
    {
        $logs = ProductOrder::orderBy('id', 'desc')
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->display('admin/order.tpl')
        );
    }

    public function ajaxQuery($request, $response, $args)
    {
        $no = $request->getParam('no');
        $user_id = $request->getParam('user_id');
        $product_name = $request->getParam('product_name');
        $order_coupon = $request->getParam('order_coupon');
        $product_type = $request->getParam('product_type');
        $order_status = $request->getParam('order_status');
        $order_payment = $request->getParam('order_payment');
        $execute_status = $request->getParam('execute_status');

        $condition = [];

        ($no !== '') && array_push($condition, ['no', 'like', '%' . $no . '%']);
        ($user_id !== '') && array_push($condition, ['user_id', '=', $user_id]);
        ($product_name !== '') && array_push($condition, ['product_name', 'like', '%' . $product_name . '%']);
        ($order_coupon !== '') && array_push($condition, ['order_coupon', '=', $order_coupon]);
        ($product_type !== 'all') && array_push($condition, ['product_type', '=', $product_type]);
        ($order_status !== 'all') && array_push($condition, ['order_status', '=', $order_status]);
        ($order_payment !== 'all') && array_push($condition, ['order_payment', '=', $order_payment]);
        ($execute_status !== 'all') && array_push($condition, ['execute_status', '=', $execute_status]);

        $results = ProductOrder::orderBy('id', 'desc')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        foreach ($results as $result) {
            $result->created_at = date('Y-m-d H:i:s', $result->created_at);
            $result->order_price = sprintf("%.2f", $result->order_price / 100);
            $result->product_price = sprintf("%.2f", $result->product_price / 100);
            $result->balance_payment = sprintf("%.2f", $result->balance_payment / 100);
            if ($result->order_status === 'paid') {
                $result->paid_at = date('Y-m-d H:i:s', $result->paid_at);
            } else {
                $result->paid_at = 'null';
            }
            if ($result->order_coupon === null) {
                $result->order_coupon = 'null';
            }
            $result->execute_status = ($result->execute_status === 0) ? '未执行' : (($result->order_status === 'refunded') ? '已撤销' : '已执行');
            $result->order_status = $result->translateOrderStatus($result->order_status, $result->expired_at);
        }

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    public function refundPreview($request, $response, $args)
    {
        $diff = [];
        $order_no = $args['no'];
        $order = ProductOrder::where('no', $order_no)->first();
        $user = User::find($order->user_id);
        $product = Product::find($order->product_id);

        $product_content = json_decode($product->content, true);
        foreach ($product_content as $key => $value) {
            switch ($key) {
                case 'product_time':
                    $array = [
                        'item' => '到期时间',
                        'before' => $user->expire_in,
                        'after' => date('Y-m-d H:i:s', strtotime($user->expire_in) - $value * 86400),
                    ];
                    $diff[] = $array;
                    break;
                case 'product_traffic':
                    $array = [
                        'item' => '账户流量',
                        'before' => round(($user->transfer_enable) / 1073741824, 2) . ' GB',
                        'after' => round(($user->transfer_enable - $value * 1073741824) / 1073741824, 2) . ' GB',
                    ];
                    $diff[] = $array;
                    break;
            }
        }

        if ($user->ref_by !== 0) {
            $invite_user = User::find($user->ref_by);
            if (isset($invite_user)) {
                $payback = Payback::where('associated_order', $order_no)->first();
                if (isset($payback) && $payback->getOriginal('fraud_detect') === 0) {
                    $array = [
                        'item' => '邀请人余额',
                        'before' => $invite_user->money,
                        'after' => sprintf("%.2f", $invite_user->money - $payback->ref_get),
                    ];
                    $diff[] = $array;
                }
            }
        }

        return $response->write(
            $this->view()
                ->assign('diff', $diff)
                ->assign('order', $order)
                ->display('admin/refund.tpl')
        );
    }

    public function refundExecution($request, $response, $args)
    {
        try {
            $order_no = $args['no'];
            $order = ProductOrder::where('no', $order_no)->first();
            if ($order->expired_at > time()) {
                // 避免样式冲突
                throw new \Exception('请稍后再试');
            }
            if ($order->order_status === 'refunded') {
                throw new \Exception('订单内容已撤销，不可重复操作');
            }
            $user = User::find($order->user_id);
            $product = Product::find($order->product_id);

            $product_content = json_decode($product->content, true);
            foreach ($product_content as $key => $value) {
                switch ($key) {
                    case 'product_time':
                        $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) - $value * 86400);
                        break;
                    case 'product_traffic':
                        $user->transfer_enable -= $value * 1073741824;
                        break;
                }
            }
            $user->save();

            if ($user->ref_by !== 0) {
                $invite_user = User::find($user->ref_by);
                if (isset($invite_user)) {
                    $payback = Payback::where('associated_order', $order_no)->first();
                    if (isset($payback) && $payback->getOriginal('fraud_detect') === 0) {
                        if ($invite_user->money < $payback->ref_get) {
                            $invite_user->money = 0;
                        } else {
                            $invite_user->money -= $payback->ref_get;
                        }
                        $invite_user->save();
                        // 更新返利状态
                        $payback->associated_order_status = 0;
                        $payback->save();
                    }
                }
            }

            // 更新订单状态
            $order->order_status = 'refunded';
            $order->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '订单内容已撤销',
        ]);
    }
}
