<?php

/*
 * Application configuration.
 * Copy this file to config.local.php and change the values for your environment.
 * config.local.php is ignored by git, so passwords never reach the repository.
 */

return [
    'app' => [
        'name' => 'GymShop',
        'debug' => true,
        // Leave null to detect it automatically (e.g. "/gymshop" when running in a XAMPP subfolder).
        'baseUrl' => null,
        'timezone' => 'Europe/Bucharest',
    ],
    'database' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'gymshop',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'logging' => [
        'file' => BASE_PATH . '/storage/logs/activity.log',
    ],
    'uploads' => [
        'directory' => BASE_PATH . '/public/uploads/products',
        'publicPath' => 'uploads/products',
        'maxSizeBytes' => 12 * 1024 * 1024,
        'allowedExtensions' => ['jpg', 'jpeg', 'png', 'webp'],
    ],
];
