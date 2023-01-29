<?php

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Sentry;

class Boot
{
    public static function setTime()
    {
        date_default_timezone_set($_ENV['timeZone']);
        View::$beginTime = microtime(true);
    }

    public static function bootDb()
    {
        // Init Eloquent ORM Connection
        $capsule = new Capsule();
        try {
            $capsule->addConnection(Config::getDbConfig());
            $capsule->getConnection()->getPdo();
        } catch (\Exception $e) {
            die('Could not connect to main database: ' . $e->getMessage());
        }

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        View::$connection = $capsule->getDatabaseManager();
        $capsule->getDatabaseManager()->connection('default')->enableQueryLog();
    }

    public static function bootSentry()
    {
        if ($_ENV['sentry_dsn'] !== '') {
            Sentry\init([
                'dsn' => $_ENV['sentry_dsn'],
                'prefixes' => [
                    realpath(__DIR__ . '/../../'),
                ],
                'in_app_exclude' => [
                    realpath(__DIR__ . '/../../vendor'),
                ],
            ]);
        }
    }
}
