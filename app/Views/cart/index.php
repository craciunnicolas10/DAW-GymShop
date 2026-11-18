<?php
/**
 * @var array<int, array{product: App\Models\Product, quantity: int, subtotal: float}> $lines
 * @var float $total
 */
?>
<div class="container">
    <header class="page-header">
        <h1>Coșul meu</h1>
    </header>

    <?php if ($lines === []): ?>
        <div class="empty-state">
            <p>Coșul tău este gol.</p>
            <a class="button" href="<?= url('/products') ?>">Descoperă produsele</a>
        </div>
    <?php else: ?>
        <div class="two-columns">
            <div class="table-wrapper">
                <table class="table table--responsive cart-table">
                    <caption>Produsele din coș</caption>
                    <thead>
                    <tr>
                        <th scope="col">Produs</th>
                        <th scope="col" class="numeric">Preț</th>
                        <th scope="col">Cantitate</th>
                        <th scope="col" class="numeric">Subtotal</th>
                        <th scope="col"><span class="visually-hidden">Acțiuni</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lines as $line): ?>
                        <?php $product = $line['product']; ?>
                        <tr>
                            <td data-label="Produs">
                                <a class="table-product" href="<?= url('/products/' . $product->getSlug()) ?>">
                                    <img src="<?= e(asset($product->getImageUrlPath())) ?>" alt="" width="56" height="56">
                                    <span><?= e($product->getName()) ?></span>
                                </a>
                            </td>
                            <td data-label="Preț" class="numeric"><?= e($product->getFormattedPrice()) ?></td>
                            <td data-label="Cantitate">
                                <form class="quantity-form" method="post" action="<?= url('/cart/update') ?>">
                                    <input type="hidden" name="productId" value="<?= e($product->getId()) ?>">
                                    <label class="visually-hidden" for="quantity-<?= e($product->getId()) ?>">Cantitate</label>
                                    <input type="number" id="quantity-<?= e($product->getId()) ?>" name="quantity"
                                           value="<?= e($line['quantity']) ?>" min="0" max="<?= e(min($product->getStock(), 20)) ?>">
                                    <button class="button button--small button--outline" type="submit">Actualizează</button>
                                </form>
                            </td>
                            <td data-label="Subtotal" class="numeric"><?= e(formatPrice($line['subtotal'])) ?></td>
                            <td>
                                <form method="post" action="<?= url('/cart/remove') ?>">
                                    <input type="hidden" name="productId" value="<?= e($product->getId()) ?>">
                                    <button class="button button--small button--danger" type="submit">Elimină</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <aside class="summary-box" aria-label="Sumar comandă">
                <h2>Sumar</h2>
                <div class="summary-row">
                    <span>Produse</span>
                    <span><?= e(formatPrice($total)) ?></span>
                </div>
                <div class="summary-row">
                    <span>Livrare</span>
                    <span>Gratuită</span>
                </div>
                <div class="summary-row summary-row--total">
                    <span>Total</span>
                    <span><?= e(formatPrice($total)) ?></span>
                </div>
                <a class="button button--block" href="<?= url('/checkout') ?>">Finalizează comanda</a>
                <p class="form-footer"><a href="<?= url('/products') ?>">Continuă cumpărăturile</a></p>
            </aside>
        </div>
    <?php endif; ?>
</div>
