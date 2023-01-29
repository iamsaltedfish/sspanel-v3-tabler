<?php

namespace App\Models;

use App\Controllers\LinkController;
use App\Services\Mail;
use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use Ramsey\Uuid\Uuid;

class User extends Model
{
    protected $connection = 'default';
    protected $table = 'user';

    /**
     * 已登录
     *
     * @var bool
     */
    public $isLogin;

    /**
     * 强制类型转换
     *
     * @var array
     */
    protected $casts = [
        't' => 'float',
        'u' => 'float',
        'd' => 'float',
        'port' => 'int',
        'transfer_enable' => 'float',
        'enable' => 'int',
        'is_admin' => 'boolean',
        'is_multi_user' => 'int',
        'node_speedlimit' => 'float',
        'ref_by' => 'int',
    ];

    /**
     * Gravatar 头像地址
     */
    public function getGravatarAttribute(): string
    {
        $hash = md5(strtolower(trim($this->email)));
        //return 'https://sdn.geekzu.org/avatar/' . $hash . '?&d=identicon';
        return 'https://gravatar.loli.net/avatar/' . $hash . '?&d=identicon';
    }

    /**
     * 联系方式类型
     */
    public function im_type(): string
    {
        switch ($this->im_type) {
            case 1:
                return '微信';
            case 2:
                return 'QQ';
            case 5:
                return 'Discord';
            default:
                return 'Telegram';
        }
    }

    /**
     * 联系方式
     */
    public function im_value(): string
    {
        switch ($this->im_type) {
            case 1:
            case 2:
            case 5:
                return $this->im_value;
            default:
                return '<a href="https://telegram.me/' . $this->im_value . '">' . $this->im_value . '</a>';
        }
    }

    /**
     * 最后使用时间
     */
    public function lastUseTime(): string
    {
        return $this->t === 0 || $this->t === null ? '没有记录' : Tools::toDateTime($this->t);
    }

    /**
     * 最后签到时间
     */
    public function lastCheckInTime(): string
    {
        return $this->last_check_in_time === 0 ? '从未签到' : Tools::toDateTime($this->last_check_in_time);
    }

    /**
     * 更新密码
     *
     * @param string $pwd
     */
    public function updatePassword(string $pwd): bool
    {
        $this->pass = Hash::passwordHash($pwd);
        return $this->save();
    }

    public function getForbiddenIp()
    {
        return str_replace(',', PHP_EOL, $this->forbidden_ip);
    }

    public function getForbiddenPort()
    {
        return str_replace(',', PHP_EOL, $this->forbidden_port);
    }

    /**
     * 更新连接密码
     *
     * @param string $pwd
     */
    public function updateSsPwd(string $pwd): bool
    {
        $this->passwd = $pwd;
        return $this->save();
    }

    /**
     * 生成邀请码
     */
    public function addInviteCode(): string
    {
        while (true) {
            $temp_code = Tools::genRandomChar(10);
            if (InviteCode::where('code', $temp_code)->first() === null) {
                if (InviteCode::where('user_id', $this->id)->count() === 0) {
                    $code = new InviteCode();
                    $code->code = $temp_code;
                    $code->user_id = $this->id;
                    $code->save();
                    return $temp_code;
                }
                return InviteCode::where('user_id', $this->id)->first()->code;
            }
        }
    }

    /**
     * 添加邀请次数
     */
    public function addInviteNum(int $num): bool
    {
        $this->invite_num += $num;
        return $this->save();
    }

    /**
     * 生成新的UUID
     */
    public function generateUUID($s): bool
    {
        $this->uuid = Uuid::uuid3(
            Uuid::NAMESPACE_DNS,
            $this->email . '|' . $s
        );
        return $this->save();
    }

    /*
     * 总流量[自动单位]
     */
    public function enableTraffic(): string
    {
        return Tools::flowAutoShow($this->transfer_enable);
    }

    /*
     * 总流量[GB]，不含单位
     */
    public function enableTrafficInGB(): float
    {
        return Tools::flowToGB($this->transfer_enable);
    }

    /*
     * 已用流量[自动单位]
     */
    public function usedTraffic(): string
    {
        return Tools::flowAutoShow($this->u + $this->d);
    }

    /*
     * 已用流量占总流量的百分比
     */
    public function trafficUsagePercent(): int
    {
        if ($this->transfer_enable === 0) {
            return 0;
        }
        $percent = ($this->u + $this->d) / $this->transfer_enable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 剩余流量[自动单位]
     */
    public function unusedTraffic(): string
    {
        return Tools::flowAutoShow($this->transfer_enable - ($this->u + $this->d));
    }

    /*
     * 剩余流量占总流量的百分比
     */
    public function unusedTrafficPercent(): int
    {
        if ($this->transfer_enable === 0) {
            return 0;
        }
        $unused = $this->transfer_enable - ($this->u + $this->d);
        $percent = $unused / $this->transfer_enable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 今天使用的流量[自动单位]
     */
    public function todayusedTraffic(): string
    {
        return Tools::flowAutoShow($this->u + $this->d - $this->last_day_t);
    }

    /*
     * 今天使用的流量占总流量的百分比
     */
    public function todayusedTrafficPercent(): int
    {
        if ($this->transfer_enable === 0) {
            return 0;
        }
        $today_used = $this->u + $this->d - $this->last_day_t;
        $percent = $today_used / $this->transfer_enable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 今天之前已使用的流量[自动单位]
     */
    public function lastUsedTraffic(): string
    {
        return Tools::flowAutoShow($this->last_day_t);
    }

    /*
     * 今天之前已使用的流量占总流量的百分比
     */
    public function lastUsedTrafficPercent(): int
    {
        if ($this->transfer_enable === 0) {
            return 0;
        }
        $Lastused = $this->last_day_t;
        $percent = $Lastused / $this->transfer_enable;
        $percent = round($percent, 2);
        $percent *= 100;
        return $percent;
    }

    /*
     * 是否可以签到
     */
    public function isAbleToCheckin(): bool
    {
        return date('Ymd') !== date('Ymd', $this->last_check_in_time);
    }

    public function getGAurl()
    {
        $ga = new GA();
        $url = $ga->getUrl(
            urlencode($_ENV['appName'] . '-' . $this->user_name . '-两步验证码'),
            $this->ga_token
        );
        return $url;
    }

    /**
     * 获取用户的邀请码
     */
    public function getInviteCodes(): ?InviteCode
    {
        return InviteCode::where('user_id', $this->id)->first();
    }

    /**
     * 删除用户的订阅链接
     */
    public function cleanLink()
    {
        Link::where('userid', $this->id)->delete();
    }

    /**
     * 获取用户的订阅链接
     */
    public function getSublink()
    {
        return LinkController::GenerateSSRSubCode($this->id);
    }

    /**
     * 删除用户的邀请码
     */
    public function clearInviteCodes()
    {
        InviteCode::where('user_id', $this->id)->delete();
    }

    /**
     * 在线 IP 个数
     */
    public function onlineIpCount(): int
    {
        // 根据 IP 分组去重
        $total = Ip::where('datetime', '>=', time() - 90)
            ->where('userid', $this->id)
            ->orderBy('userid', 'desc')
            ->groupBy('ip')
            ->get();
        $ip_list = [];
        foreach ($total as $single_record) {
            $ip = Tools::getRealIp($single_record->ip);
            if (Node::where('node_ip', $ip)->first() !== null) {
                continue;
            }
            $ip_list[] = $ip;
        }
        return count($ip_list);
    }

    /**
     * 销户
     */
    public function killUser(): bool
    {
        $uid = $this->id;
        $email = $this->email;

        Ip::where('userid', '=', $uid)->delete();
        Link::where('userid', '=', $uid)->delete();
        Token::where('user_id', '=', $uid)->delete();
        LoginIp::where('userid', '=', $uid)->delete();
        DetectLog::where('user_id', '=', $uid)->delete();
        InviteCode::where('user_id', '=', $uid)->delete();
        EmailVerify::where('email', '=', $email)->delete();
        PasswordReset::where('email', '=', $email)->delete();
        TelegramSession::where('user_id', '=', $uid)->delete();
        UserSubscribeLog::where('user_id', '=', $uid)->delete();

        $this->delete();
        return true;
    }

    /**
     * 获取累计收入
     *
     * @param string $req
     */
    public function calIncome(string $req): string
    {
        $condition = [];
        switch ($req) {
            case "yesterday":
                $start = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
                $stop = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
                break;
            case "today":
                $start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $stop = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
                break;
            case "this month":
                $start = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $stop = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
                break;
            case "last month":
                $start = date('Y-m-01 00:00:00', strtotime('-1 month'));
                $stop = date('Y-m-d 23:59:59', strtotime(-date('d') . 'day'));
                break;
            default:
                $start = null;
                break;
        }

        array_push($condition, ['order_status', 'paid']);
        if ($start !== null) {
            array_push($condition, ['created_at', '>', $start]);
            array_push($condition, ['created_at', '<', $stop]);
        }

        $amount = ProductOrder::where($condition)
            ->where('product_type', '!=', 'recharge')
            ->sum('order_price');

        return is_null($amount) ? 0.00 : sprintf("%.2f", $amount / 100);
    }

    /**
     * 解绑 Telegram
     */
    public function telegramReset(): array
    {
        $return = [
            'ok' => true,
            'msg' => '解绑成功.',
        ];

        $telegram_id = $this->telegram_id;
        $this->telegram_id = 0;

        if (!$this->save()) {
            $return = [
                'ok' => false,
                'msg' => '解绑失败.',
            ];
        }

        return $return;
    }

    /**
     * 更新端口
     *
     * @param int $Port
     */
    public function setPort($Port): array
    {
        $PortOccupied = User::pluck('port')->toArray();
        if (in_array($Port, $PortOccupied) === true) {
            return [
                'ok' => false,
                'msg' => '端口已被占用',
            ];
        }
        $this->port = $Port;
        $this->save();
        return [
            'ok' => true,
            'msg' => $this->port,
        ];
    }

    /**
     * 重置端口
     */
    public function resetPort(): array
    {
        $Port = Tools::getAvPort();
        $this->setPort($Port);
        $this->save();
        return [
            'ok' => true,
            'msg' => $this->port,
        ];
    }

    /**
     * 发送邮件
     *
     * @param string $subject
     * @param string $template
     * @param array  $ary
     * @param array  $files
     */
    public function sendMail(string $subject, string $template, string $type, array $ary = [], array $files = [], $is_queue = false): bool
    {
        $result = false;
        if ($is_queue) {
            $new_emailqueue = new EmailQueue();
            $new_emailqueue->to_email = $this->email;
            $new_emailqueue->subject = $subject;
            $new_emailqueue->template = $template;
            $new_emailqueue->time = time();
            $ary = array_merge(['user' => $this], $ary);
            $new_emailqueue->array = json_encode($ary);
            $new_emailqueue->save();
            return true;
        }
        // 验证邮箱地址是否正确
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            // 发送邮件
            try {
                Mail::send(
                    $this->email,
                    $subject,
                    $template,
                    $type,
                    array_merge(
                        [
                            'user' => $this,
                        ],
                        $ary
                    ),
                    $files
                );
                $result = true;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return $result;
    }

    /**
     * 记录登录 IP
     *
     * @param string $ip
     * @param int    $type 登录失败为 1
     */
    public function collectLoginIP(string $ip, string $ua, int $type = 0): bool
    {
        $loginip = new LoginIp();
        $loginip->ip = $ip;
        $loginip->ua = $ua;
        $loginip->attribution = Tools::getIpInfo($ip);
        $loginip->userid = $this->id;
        $loginip->datetime = time();
        $loginip->type = $type;

        return $loginip->save();
    }

    public function getMailUnsubToken(): string
    {
        $item = MailPush::where('user_id', $this->id)->first();
        return $item->access_token;
    }
}
