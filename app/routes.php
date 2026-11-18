<?php

declare(strict_types=1);

/*
 * Application routes: URL => [Controller, action].
 * @var App\Core\Router $router
 */

use App\Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);
