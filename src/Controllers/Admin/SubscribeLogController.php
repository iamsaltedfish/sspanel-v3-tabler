<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\UserSubscribeLog;
use App\Utils\Tools;

class SubscribeLogController extends AdminController
{
    public function index($request, $response, $args)
    {
        $logs = UserSubscribeLog::orderBy('id', 'desc')
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->registerClass('Tools', Tools::class)
                ->display('admin/subscribe.tpl')
        );
    }

    public function subscribeAjax($request, $response, $args)
    {
        $email = $request->getParam('email');
        $user_id = $request->getParam('user_id');
        $user_name = $request->getParam('user_name');
        $request_ip = $request->getParam('request_ip');
        $subscribe_type = $request->getParam('subscribe_type');
        $request_user_agent = $request->getParam('request_user_agent');

        $condition = [];

        ($email !== '') && array_push($condition, ['email', '=', $email]);
        ($user_id !== '') && array_push($condition, ['user_id', '=', $user_id]);
        ($user_name !== '') && array_push($condition, ['user_name', '=', $user_name]);
        ($request_ip !== '') && array_push($condition, ['request_ip', '=', $request_ip]);
        ($subscribe_type !== '') && array_push($condition, ['subscribe_type', '=', $subscribe_type]);
        ($request_user_agent !== '') && array_push($condition, ['request_user_agent', 'like', '%' . $request_user_agent . '%']);

        $results = UserSubscribeLog::orderBy('id', 'desc')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        foreach ($results as $result) {
            $result->ip_info = Tools::getIpInfo($result->request_ip);
        }

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }
}
