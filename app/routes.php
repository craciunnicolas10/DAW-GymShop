<?php

declare(strict_types=1);

/*
 * Application routes: URL => [Controller, action].
 * @var App\Core\Router $router
 */

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\ProductController;

// Public pages
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);
$router->get('/about', [PageController::class, 'about']);

// Authentication
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);
