<?php
/**
 * @var App\Models\Product $product
 */
$productUrl = url('/products/' . $product->getSlug());
?>
<article class="product-card">
    <div class="product-card__badges">
        <?php if (!$product->isInStock()): ?>
            <span class="badge badge--danger">Stoc epuizat</span>
        <?php elseif ($product->isLowStock()): ?>
            <span class="badge badge--warning">Ultimele bucăți</span>
        <?php endif; ?>
    </div>
    <a class="product-card__image" href="<?= e($productUrl) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= e(asset($product->getImageUrlPath())) ?>" alt="<?= e($product->getName()) ?>" width="400" height="400" loading="lazy">
    </a>
    <div class="product-card__body">
        <span class="product-card__brand"><?= e($product->getBrandName()) ?></span>
        <h3 class="product-card__title"><a href="<?= e($productUrl) ?>"><?= e($product->getName()) ?></a></h3>
        <div class="product-card__footer">
            <span class="price"><?= e($product->getFormattedPrice()) ?></span>
            <?php if ($product->isInStock()): ?>
                <form method="post" action="<?= url('/cart/add') ?>">
                    <input type="hidden" name="productId" value="<?= e($product->getId()) ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button class="button button--small" type="submit" aria-label="Adaugă <?= e($product->getName()) ?> în coș">În coș</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</article>
