<?php

declare(strict_types=1);

use App\Core\Config;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

date_default_timezone_set('Europe/Bucharest');

Config::load([
    'app' => [
        'name' => 'GymShop',
        'debug' => false,
        'baseUrl' => '/gymshop',
        'timezone' => 'Europe/Bucharest',
    ],
    'logging' => [
        'file' => sys_get_temp_dir() . '/gymshop-tests/activity.log',
    ],
]);
