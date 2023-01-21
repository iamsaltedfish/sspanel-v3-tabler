<?php

namespace App\Services\Aws;

use App\Models\Setting;
use Aws\Sdk;

class Factory
{
    public static function createAwsClient()
    {
        $configs = Setting::getClass('aws_ses');

        $sdk = new Sdk([
            'credentials' => [
                'key' => $configs['aws_access_key_id'],
                'secret' => $configs['aws_secret_access_key'],
            ],
            'region' => $_ENV['aws_region'],
            'version' => 'latest',
            'DynamoDb' => [
                'region' => $_ENV['aws_region'],
            ],
        ]);
        return $sdk;
    }

    public static function createDynamodb()
    {
        return self::createAwsClient()->createDynamoDb();
    }

    public static function createSes()
    {
    }
}
