<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

class ConfigController extends AdminController
{
    public function index($request, $response, $args)
    {
        $alls = [];
        $hidden_list = ['key', 'muKey', 'db_host', 'db_database', 'db_username', 'db_password', 'cdn_forwarded_ip', 'adminApiToken', 'mail_push_salt', 'telegram_token', 'telegram_request_token'];
        foreach ($_ENV as $key => $value) {
            if (!in_array($key, $hidden_list, true)) {
                $alls[] = [
                    'key' => $key,
                    'type' => gettype($value),
                    'value' => is_array($value) ? json_encode($value, 320) : $value == '' ? 'empty string' : is_bool($value) ? $value === true ? 'true' : 'false' : htmlspecialchars($value),
                ];
            }
        }

        return $response->write(
            $this->view()
                ->assign('count', 1)
                ->assign('alls', $alls)
                ->display('admin/config.tpl')
        );
    }
}
