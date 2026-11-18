<?php

declare(strict_types=1);

/*
 * Application routes: URL => [Controller, action].
 * @var App\Core\Router $router
 */

use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Admin\AdminLogController;
use App\Controllers\Admin\AdminOrderController;
use App\Controllers\Admin\AdminProductController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
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

// Shopping cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

// Orders (authenticated users)
$router->get('/checkout', [OrderController::class, 'checkout']);
$router->post('/checkout', [OrderController::class, 'placeOrder']);
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/{id}', [OrderController::class, 'show']);

// Administration (admin role only)
$router->get('/admin', [AdminDashboardController::class, 'index']);
$router->get('/admin/products', [AdminProductController::class, 'index']);
$router->get('/admin/products/create', [AdminProductController::class, 'create']);
$router->post('/admin/products', [AdminProductController::class, 'store']);
$router->get('/admin/products/{id}/edit', [AdminProductController::class, 'edit']);
$router->post('/admin/products/{id}/update', [AdminProductController::class, 'update']);
$router->post('/admin/products/{id}/delete', [AdminProductController::class, 'delete']);
$router->get('/admin/orders', [AdminOrderController::class, 'index']);
$router->post('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
$router->get('/admin/logs', [AdminLogController::class, 'index']);
$router->get('/admin/logs/download', [AdminLogController::class, 'download']);
