<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Models\Order;
use App\Repositories\OrderRepository;

final class AdminOrderController extends AdminController
{
    private OrderRepository $orderRepository;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->orderRepository = new OrderRepository(Database::getConnection());
    }

    public function index(): void
    {
        $this->renderAdmin('admin/orders/index', 'orders', [
            'pageTitle' => 'Administrare comenzi',
            'orders' => $this->orderRepository->findAll(),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $order = $this->orderRepository->findById((int) $id);
        if ($order === null) {
            throw new NotFoundException('Comanda nu există.');
        }

        $status = $this->request->input('status');

        if (!Order::isValidStatus($status)) {
            $this->session->flash('error', 'Statusul ales nu este valid.');
            $this->redirect('/admin/orders');
        }

        $this->orderRepository->updateStatus($order->getId(), $status);
        $this->logActivity('ADMIN_ORDER_STATUS', [
            'orderId' => $order->getId(),
            'from' => $order->getStatus(),
            'to' => $status,
        ]);
        $this->session->flash('success', "Statusul comenzii #{$order->getId()} a fost schimbat în „" . Order::STATUS_LABELS[$status] . '”.');
        $this->redirect('/admin/orders');
    }
}
