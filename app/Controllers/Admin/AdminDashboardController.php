<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

final class AdminDashboardController extends AdminController
{
    public function index(): void
    {
        $pdo = Database::getConnection();
        $productRepository = new ProductRepository($pdo);
        $orderRepository = new OrderRepository($pdo);
        $userRepository = new UserRepository($pdo);

        $this->renderAdmin('admin/dashboard', 'dashboard', [
            'pageTitle' => 'Panou de administrare',
            'productCount' => $productRepository->countAll(),
            'lowStockCount' => $productRepository->countLowStock(),
            'orderCount' => $orderRepository->countAll(),
            'userCount' => $userRepository->countAll(),
            'revenue' => $orderRepository->getTotalRevenue(),
            'recentOrders' => array_slice($orderRepository->findAll(), 0, 5),
        ]);
    }
}
