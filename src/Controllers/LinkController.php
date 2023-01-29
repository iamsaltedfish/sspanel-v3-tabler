<?php

namespace App\Controllers;

use App\Models\Link;
use App\Utils\Tools;

class LinkController extends BaseController
{
    public static function generateUniqueToken(): string
    {
        while (true) {
            $random_str = Tools::genRandomChar(16);
            $exist = Link::where('token', $random_str)->first();
            if (!isset($exist)) {
                break;
            }
        }

        return $random_str;
    }

    public static function getSubscriptionToken(int $userid): string
    {
        $exist = Link::where('userid', $userid)->first();
        if (isset($exist)) {
            return $exist->token;
        }

        $record = new Link();
        $record->userid = $userid;
        $record->token = self::generateUniqueToken();
        $record->save();

        return $record->token;
    }

    public static function getTheClientLink($user)
    {
        $link = $_ENV['subUrl'] . self::getSubscriptionToken($user->id);
        $list = [
            'v2ray' => '?sub=3',
            'clash' => '?clash=1',
            'quantumult' => '?quantumult=1',
        ];

        return array_map(
            static function ($suffix) use ($link) {
                return $link . $suffix;
            },
            $list
        );
    }
}
