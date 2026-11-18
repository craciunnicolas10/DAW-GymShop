<?php
/**
 * @var string $activeSection
 * @var int $productCount
 * @var int $lowStockCount
 * @var int $orderCount
 * @var int $userCount
 * @var float $revenue
 * @var App\Models\Order[] $recentOrders
 */
?>
<div class="container">
    <header class="page-header">
        <h1>Panou de administrare</h1>
    </header>

    <?= App\Core\View::renderFile('admin/partials/nav', ['activeSection' => $activeSection]) ?>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-card__label">Produse</span>
            <span class="stat-card__value"><?= e($productCount) ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Stoc redus / epuizat</span>
            <span class="stat-card__value"><?= e($lowStockCount) ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Comenzi</span>
            <span class="stat-card__value"><?= e($orderCount) ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Vânzări (fără anulate)</span>
            <span class="stat-card__value"><?= e(formatPrice($revenue)) ?></span>
        </div>
    </div>

    <section aria-labelledby="recent-orders-title">
        <div class="section-header">
            <h2 id="recent-orders-title">Comenzi recente</h2>
            <a href="<?= url('/admin/orders') ?>">Toate comenzile &rarr;</a>
        </div>
        <div class="table-wrapper">
            <table class="table table--responsive">
                <thead>
                <tr>
                    <th scope="col">Comandă</th>
                    <th scope="col">Client</th>
                    <th scope="col">Data</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="numeric">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td data-label="Comandă"><a href="<?= url('/orders/' . $order->getId()) ?>">#<?= e($order->getId()) ?></a></td>
                        <td data-label="Client"><?= e($order->getCustomerName()) ?></td>
                        <td data-label="Data"><?= e(date('d.m.Y H:i', strtotime((string) $order->getCreatedAt()))) ?></td>
                        <td data-label="Status"><span class="badge badge--status-<?= e($order->getStatus()) ?>"><?= e($order->getStatusLabel()) ?></span></td>
                        <td data-label="Total" class="numeric"><?= e(formatPrice($order->getTotal())) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted">Utilizatori înregistrați: <?= e($userCount) ?></p>
    </section>
</div>
