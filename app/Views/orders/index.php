<?php
/**
 * @var App\Models\Order[] $orders
 */
?>
<div class="container">
    <header class="page-header">
        <h1>Comenzile mele</h1>
    </header>

    <?php if ($orders === []): ?>
        <div class="empty-state">
            <p>Nu ai plasat încă nicio comandă.</p>
            <a class="button" href="<?= url('/products') ?>">Începe cumpărăturile</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table table--responsive">
                <caption>Istoricul comenzilor</caption>
                <thead>
                <tr>
                    <th scope="col">Comandă</th>
                    <th scope="col">Data</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="numeric">Total</th>
                    <th scope="col"><span class="visually-hidden">Detalii</span></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td data-label="Comandă">#<?= e($order->getId()) ?></td>
                        <td data-label="Data"><?= e(date('d.m.Y H:i', strtotime((string) $order->getCreatedAt()))) ?></td>
                        <td data-label="Status"><span class="badge badge--status-<?= e($order->getStatus()) ?>"><?= e($order->getStatusLabel()) ?></span></td>
                        <td data-label="Total" class="numeric"><?= e(formatPrice($order->getTotal())) ?></td>
                        <td><a href="<?= url('/orders/' . $order->getId()) ?>">Vezi detalii</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
