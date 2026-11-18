<?php

declare(strict_types=1);

/*
 * Bootstrap file: loads the classes, the configuration and starts the session.
 * It is included by public/index.php on every request.
 */

use App\Core\Config;

define('BASE_PATH', dirname(__DIR__));

// PSR-4 style autoloader: App\Core\Router => app/Core/Router.php
spl_autoload_register(static function (string $className): void {
    $prefix = 'App\\';

    if (!str_starts_with($className, $prefix)) {
        return;
    }

    $relativeClass = substr($className, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/helpers.php';

$localConfigFile = BASE_PATH . '/config/config.local.php';
Config::load(require (is_file($localConfigFile) ? $localConfigFile : BASE_PATH . '/config/config.example.php'));

date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Bucharest'));

if (Config::get('app.debug', false)) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

// Detect the base URL when the site runs in a subfolder (e.g. http://localhost/gymshop).
if (Config::get('app.baseUrl') === null) {
    $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $baseUrl = preg_replace('#/public$#', '', rtrim($scriptDirectory, '/'));
    Config::set('app.baseUrl', $baseUrl === '/' ? '' : $baseUrl);
}

if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli') {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('GYMSHOPSESSID');
    session_start();
}
