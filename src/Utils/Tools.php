<?php

namespace App\Utils;

use App\Models\Link;
use App\Models\Model;
use App\Models\User;
use App\Services\Config;
use ZipArchive;

class Tools
{
    /**
     * 验证邮箱格式
     */
    public static function emailCheck($address)
    {
        return filter_var($address, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    /**
     * 验证合法邮箱域
     */
    public static function isEmailLegal(string $email): bool
    {
        if ($_ENV['mail_filter'] !== 0) {
            $mail_suffix = explode('@', $email)[1];
            if ($_ENV['mail_filter'] === 1) {
                // 白名单模式
                if (!in_array($mail_suffix, $_ENV['mail_filter_list'])) {
                    return false;
                }
            } else {
                // 黑名单模式
                if (in_array($mail_suffix, $_ENV['mail_filter_list'])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 查询IP归属
     */
    public static function getIpInfo($ip)
    {
        $iplocation = new QQWry();
        $location = $iplocation->getlocation($ip);
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    /**
     * 根据流量值自动转换单位输出
     */
    public static function flowAutoShow($value = 0)
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        if (abs($value) > $pb) {
            return round($value / $pb, 2) . 'PB';
        }

        if (abs($value) > $tb) {
            return round($value / $tb, 2) . 'TB';
        }

        if (abs($value) > $gb) {
            return round($value / $gb, 2) . 'GB';
        }

        if (abs($value) > $mb) {
            return round($value / $mb, 2) . 'MB';
        }

        if (abs($value) > $kb) {
            return round($value / $kb, 2) . 'KB';
        }

        return round($value, 2) . 'B';
    }

    /**
     * 根据含单位的流量值转换 B 输出
     */
    public static function flowAutoShowZ($Value)
    {
        $number = substr($Value, 0, -2);
        if (!is_numeric($number)) {
            return null;
        }
        $unit = strtoupper(substr($Value, -2));
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        switch ($unit) {
            case 'B':
                $number = round($number, 2);
                break;
            case 'KB':
                $number = round($number * $kb, 2);
                break;
            case 'MB':
                $number = round($number * $mb, 2);
                break;
            case 'GB':
                $number = round($number * $gb, 2);
                break;
            case 'TB':
                $number = round($number * $tb, 2);
                break;
            case 'PB':
                $number = round($number * $pb, 2);
                break;
            default:
                return null;
                break;
        }
        return $number;
    }

    //虽然名字是toMB，但是实际上功能是from MB to B
    public static function toMB($traffic)
    {
        $mb = 1048576;
        return $traffic * $mb;
    }

    //虽然名字是toGB，但是实际上功能是from GB to B
    public static function toGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic * $gb;
    }

    public static function flowToGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic / $gb;
    }

    public static function flowToMB($traffic)
    {
        $gb = 1048576;
        return $traffic / $gb;
    }

    //获取随机字符串

    public static function genRandomNum($length = 8)
    {
        // 来自Miku的 6位随机数 注册验证码 生成方案
        $chars = '0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function genRandomChar($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function isIp($a)
    {
        return preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $a);
    }

    // Unix time to Date Time
    public static function toDateTime($time)
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function getAvPort()
    {
        if ($_ENV['min_port'] > 65535 || $_ENV['min_port'] <= 0 || $_ENV['max_port'] > 65535 || $_ENV['max_port'] <= 0) {
            return 0;
        }
        $det = User::pluck('port')->toArray();
        $port = array_diff(range($_ENV['min_port'], $_ENV['max_port']), $det);
        shuffle($port);
        return $port[0];
    }

    public static function base64UrlEncode($input)
    {
        return strtr(base64_encode($input), ['+' => '-', '/' => '_', '=' => '']);
    }

    public static function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function getDir($dir)
    {
        $dirArray = [];
        $handle = opendir($dir);
        if ($handle !== false) {
            $i = 0;
            while (($file = readdir($handle)) !== false) {
                if ($file !== '.' && $file !== '..' && !strpos($file, '.')) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }
            closedir($handle);
        }
        return $dirArray;
    }

    public static function isParamValidate($type, $str)
    {
        $list = Config::getSupportParam($type);
        if (in_array($str, $list)) {
            return true;
        }
        return false;
    }

    /**
     * Filter key in `App\Models\Model` object
     *
     * @param Model $object
     * @param array $filter_array
     *
     * @return Model
     */
    public static function keyFilter($object, $filter_array)
    {
        foreach ($object->toArray() as $key => $value) {
            if (!in_array($key, $filter_array)) {
                unset($object->$key);
            }
        }
        return $object;
    }

    public static function getRealIp($rawIp)
    {
        return str_replace('::ffff:', '', $rawIp);
    }

    /**
     * Add files and sub-directories in a folder to zip file.
     *
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    public static function folderToZip(string $folder, ZipArchive &$zipFile, int $exclusiveLength): void
    {
        $handle = opendir($folder);
        while (($f = readdir($handle)) !== false) {
            if ($f !== '.' && $f !== '..') {
                $filePath = "${folder}/${f}";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * 重置自增列 ID
     *
     * @param DatatablesHelper $db
     * @param string $table
     */
    public static function resetAutoIncrement($db, $table)
    {
        $maxid = $db->query(
            "SELECT `auto_increment` AS `maxid` FROM `information_schema`.`tables` WHERE `table_schema` = '" . $_ENV['db_database'] . "' AND `table_name` = '" . $table . "'"
        )[0]['maxid'];
        if ($maxid >= 2000000000) {
            $db->query('ALTER TABLE `' . $table . '` auto_increment = 1');
        }
    }

    public static function etag($data)
    {
        return sha1(json_encode($data));
    }

    public static function genSubToken()
    {
        for ($i = 0; $i < 10; $i++) {
            $token = self::genRandomChar(16);
            $is_token_used = Link::where('token', $token)->first();
            if ($is_token_used === null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }
}
