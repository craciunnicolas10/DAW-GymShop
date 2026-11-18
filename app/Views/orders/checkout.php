<?php
/**
 * @var array<int, array{product: App\Models\Product, quantity: int, subtotal: float}> $lines
 * @var float $total
 * @var array<string, string> $errors
 * @var array<string, string> $old
 */
?>
<div class="container">
    <header class="page-header">
        <ol class="breadcrumbs">
            <li><a href="<?= url('/cart') ?>">Coș</a></li>
            <li>Finalizare comandă</li>
        </ol>
        <h1>Finalizare comandă</h1>
    </header>

    <div class="two-columns">
        <form class="content-card" method="post" action="<?= url('/checkout') ?>" novalidate>
            <h2>Date de livrare</h2>

            <div class="form-group<?= isset($errors['shippingAddress']) ? ' has-error' : '' ?>">
                <label for="shippingAddress">Adresă de livrare</label>
                <textarea id="shippingAddress" name="shippingAddress" maxlength="255" required
                          placeholder="Stradă, număr, bloc, apartament, oraș, județ"><?= e($old['shippingAddress'] ?? '') ?></textarea>
                <?php if (isset($errors['shippingAddress'])): ?>
                    <span class="form-error"><?= e($errors['shippingAddress']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group<?= isset($errors['phone']) ? ' has-error' : '' ?>">
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" placeholder="07xxxxxxxx" required>
                <?php if (isset($errors['phone'])): ?>
                    <span class="form-error"><?= e($errors['phone']) ?></span>
                <?php endif; ?>
            </div>

            <p class="text-muted">Plata se face ramburs, la livrare.</p>
            <button class="button button--block" type="submit">Plasează comanda (<?= e(formatPrice($total)) ?>)</button>
        </form>

        <aside class="summary-box" aria-label="Produse comandate">
            <h2>Produse</h2>
            <?php foreach ($lines as $line): ?>
                <div class="summary-row">
                    <span><?= e($line['quantity']) ?> &times; <?= e($line['product']->getName()) ?></span>
                    <span><?= e(formatPrice($line['subtotal'])) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row summary-row--total">
                <span>Total</span>
                <span><?= e(formatPrice($total)) ?></span>
            </div>
        </aside>
    </div>
</div>
