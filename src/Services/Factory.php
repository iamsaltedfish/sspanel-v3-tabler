<?php

namespace App\Services;

use App\Services\Auth\Cookie;

class Factory
{
    public static function createAuth()
    {
        //$method = $_ENV['authDriver'];
        return new Cookie();
    }
}
