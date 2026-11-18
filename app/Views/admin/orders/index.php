<?php
/**
 * @var string $activeSection
 * @var App\Models\Order[] $orders
 */
?>
<div class="container">
    <header class="page-header">
        <h1>Comenzi</h1>
    </header>

    <?= App\Core\View::renderFile('admin/partials/nav', ['activeSection' => $activeSection]) ?>

    <div class="table-wrapper">
        <table class="table table--responsive">
            <caption><?= count($orders) ?> comenzi</caption>
            <thead>
            <tr>
                <th scope="col">Comandă</th>
                <th scope="col">Client</th>
                <th scope="col">Data</th>
                <th scope="col" class="numeric">Total</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td data-label="Comandă"><a href="<?= url('/orders/' . $order->getId()) ?>">#<?= e($order->getId()) ?></a></td>
                    <td data-label="Client">
                        <?= e($order->getCustomerName()) ?><br>
                        <small class="text-muted"><?= e($order->getCustomerEmail()) ?></small>
                    </td>
                    <td data-label="Data"><?= e(date('d.m.Y H:i', strtotime((string) $order->getCreatedAt()))) ?></td>
                    <td data-label="Total" class="numeric"><?= e(formatPrice($order->getTotal())) ?></td>
                    <td data-label="Status">
                        <form class="quantity-form" method="post" action="<?= url('/admin/orders/' . $order->getId() . '/status') ?>">
                            <label class="visually-hidden" for="status-<?= e($order->getId()) ?>">Status comanda #<?= e($order->getId()) ?></label>
                            <select id="status-<?= e($order->getId()) ?>" name="status">
                                <?php foreach (App\Models\Order::STATUS_LABELS as $status => $label): ?>
                                    <option value="<?= e($status) ?>" <?= $order->getStatus() === $status ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="button button--small button--dark" type="submit">Salvează</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
