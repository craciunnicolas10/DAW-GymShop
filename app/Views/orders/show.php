<?php
/**
 * @var App\Models\Order $order
 * @var array|null $currentUser
 */
?>
<div class="container">
    <header class="page-header">
        <ol class="breadcrumbs">
            <li><a href="<?= url('/orders') ?>">Comenzile mele</a></li>
            <li>Comanda #<?= e($order->getId()) ?></li>
        </ol>
        <h1>Comanda #<?= e($order->getId()) ?></h1>
        <p>
            <span class="badge badge--status-<?= e($order->getStatus()) ?>"><?= e($order->getStatusLabel()) ?></span>
            <span class="text-muted">plasată la <?= e(date('d.m.Y H:i', strtotime((string) $order->getCreatedAt()))) ?></span>
        </p>
    </header>

    <div class="two-columns">
        <div class="table-wrapper">
            <table class="table table--responsive">
                <caption>Produse comandate</caption>
                <thead>
                <tr>
                    <th scope="col">Produs</th>
                    <th scope="col" class="numeric">Preț unitar</th>
                    <th scope="col" class="numeric">Cantitate</th>
                    <th scope="col" class="numeric">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($order->getItems() as $item): ?>
                    <tr>
                        <td data-label="Produs">
                            <?php if ($item->getProductSlug() !== null): ?>
                                <a href="<?= url('/products/' . $item->getProductSlug()) ?>"><?= e($item->getProductName()) ?></a>
                            <?php else: ?>
                                <?= e($item->getProductName()) ?>
                            <?php endif; ?>
                        </td>
                        <td data-label="Preț unitar" class="numeric"><?= e(formatPrice($item->getUnitPrice())) ?></td>
                        <td data-label="Cantitate" class="numeric"><?= e($item->getQuantity()) ?></td>
                        <td data-label="Subtotal" class="numeric"><?= e(formatPrice($item->getSubtotal())) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">Total</td>
                    <td class="numeric"><?= e(formatPrice($order->getTotal())) ?></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <aside class="summary-box" aria-label="Date de livrare">
            <h2>Livrare</h2>
            <?php if ($order->getCustomerName() !== null): ?>
                <p><strong>Client:</strong> <?= e($order->getCustomerName()) ?></p>
            <?php endif; ?>
            <p><strong>Adresă:</strong> <?= e($order->getShippingAddress()) ?></p>
            <p><strong>Telefon:</strong> <?= e($order->getPhone()) ?></p>
            <p><strong>Plată:</strong> ramburs</p>
        </aside>
    </div>
</div>
