<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\PhinxLog;
use App\Models\User;

class CheckController extends AdminController
{
    public static function getfilecounts(string $directory): int
    {
        $handle = opendir($directory);
        $i = 0;
        while (true) {
            $file = readdir($handle);
            if (!$file) {
                break;
            }
            if ($file !== '.' && $file !== '..') {
                $i++;
            }
        }
        closedir($handle);
        return $i;
    }

    public function index($request, $response, $args)
    {
        $alls = [];
        // 检查是否开启开发者模式
        $alls[] = [
            'item' => '开发模式',
            'status' => $_ENV['debug'] ? '警告' : '通过',
            'description' => $_ENV['debug'] ? '站点运行在开发模式下，这会导致在出错的页面中泄露敏感参数。请考虑关闭' : '未启用开发模式，此处不存在风险',
        ];
        // 检查是否存在没有合并的迁移文件
        $migrated_files_count = self::getfilecounts(dirname(__FILE__, 4) . '/databases/migrations');
        $alls[] = [
            'item' => '迁移文件',
            'status' => PhinxLog::count() !== $migrated_files_count ? '警告' : '通过',
            'description' => PhinxLog::count() !== $migrated_files_count ? '存在没有合并的迁移文件，这将影响部分功能的正常使用。在网站根目录下执行 <code>vendor/bin/phinx migrate</code> 尝试修复' : '所有迁移文件都已经执行',
        ];
        // 检查是否存在ip数据库
        $ip_data_path = dirname(__FILE__, 4) . '/storage/qqwry.dat';
        $alls[] = [
            'item' => 'IP数据库',
            'status' => !file_exists($ip_data_path) ? '警告' : '通过',
            'description' => !file_exists($ip_data_path) ? '没有找到IP数据库文件。在网站根目录下执行 <code>php xcat Tool initQQWry</code> 尝试修复' : 'IP数据库文件存在',
        ];
        // 检查是否存在多个管理员账户
        $alls[] = [
            'item' => '管理员账户',
            'status' => User::where('is_admin', 1)->count() > 1 ? '注意' : '通过',
            'description' => User::where('is_admin', 1)->count() > 1 ? '存在多个管理员账户' : '系统中只有一个管理员账户',
        ];
        // 检查cookie的salt
        $alls[] = [
            'item' => 'Cookie Salt',
            'status' => $_ENV['key'] === '32150285b345c48aa3492f9212f61ca2' ? '警告' : '通过',
            'description' => $_ENV['key'] === '32150285b345c48aa3492f9212f61ca2' ? '正在使用默认的盐用于加密 Cookie 凭证，这是一个潜在的安全隐患' : '正在使用自定义的盐保护 Cookie 凭证',
        ];
        // 检查站点域名
        $alls[] = [
            'item' => '站点域名',
            'status' => $_ENV['baseUrl'] === 'https://domain.com' ? '警告' : '通过',
            'description' => $_ENV['baseUrl'] === 'https://domain.com' ? '没有设置正确的站点地址，这将导致站点和邮件内的链接无效' : '设置了正确的站点地址',
        ];

        return $response->write(
            $this->view()
                ->assign('alls', $alls)
                ->assign('count', 1)
                ->display('admin/check.tpl')
        );
    }
}
