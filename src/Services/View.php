<?php

namespace App\Services;

use Smarty;

class View
{
    public static $connection;
    public static $beginTime;

    public static function getSmarty()
    {
        $smarty = new Smarty(); //实例化smarty
        $user = Auth::getUser();

        $theme = $user->isLogin ? $user->theme : $_ENV['theme'];
        $smarty->settemplatedir(BASE_PATH . '/resources/views/' . $theme . '/'); //设置模板文件存放目录
        $smarty->setcompiledir(BASE_PATH . '/storage/framework/smarty/compile/'); //设置生成文件存放目录
        $smarty->setcachedir(BASE_PATH . '/storage/framework/smarty/cache/'); //设置缓存文件存放目录
        // add config
        $smarty->assign('user', $user);
        $smarty->assign('config', Config::getPublicConfig());

        return $smarty;
    }
}
