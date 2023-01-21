<?php

namespace App\Services;

use Firebase\JWT\JWT as JwtClient;

class Jwt
{
    private static function getKey()
    {
        return $_ENV['key'];
    }

    public static function encode($input)
    {
        return JwtClient::encode($input, self::getKey());
    }

    public static function encodeWithkey($input, $key)
    {
        return JwtClient::encode($input, $key);
    }

    public static function decodeArray($input)
    {
        return JWT::decode($input, self::getKey(), ['HS256']);
    }
}
