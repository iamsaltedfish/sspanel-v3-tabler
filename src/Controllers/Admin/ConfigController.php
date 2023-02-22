<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

class ConfigController extends AdminController
{
    public function index($request, $response, $args)
    {
        $alls = [];
        $hidden_list = ['key', 'muKey', 'db_host', 'db_database', 'db_username', 'db_password', 'cdn_forwarded_ip', 'adminApiToken', 'mail_push_salt', 'telegram_token', 'telegram_request_token', 'active_payments'];
        foreach ($_ENV as $key => $value) {
            while (true) {
                if (is_array($value)) {
                    $display_value = htmlspecialchars(json_encode($value, 320));
                    break;
                }
                if ($value === '') {
                    $display_value = 'empty string';
                    break;
                }
                if (is_bool($value)) {
                    $display_value = $value === true ? 'true' : 'false';
                    break;
                }
                $display_value = htmlspecialchars($value);
                break;
            }
            if (!in_array($key, $hidden_list, true)) {
                $alls[] = [
                    'key' => $key,
                    'type' => gettype($value),
                    'value' => $display_value,
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
