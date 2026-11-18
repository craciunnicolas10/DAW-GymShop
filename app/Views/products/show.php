<?php
/**
 * @var App\Models\Product $product
 * @var App\Models\Product[] $relatedProducts
 */
$pricePerServing = $product->getPricePerServing();
?>
<div class="container">
    <header class="page-header">
        <ol class="breadcrumbs">
            <li><a href="<?= url('/') ?>">Acasă</a></li>
            <li><a href="<?= url('/products') ?>">Produse</a></li>
            <li><a href="<?= url('/products?category=' . urlencode((string) $product->getCategorySlug())) ?>"><?= e($product->getCategoryName()) ?></a></li>
            <li><?= e($product->getName()) ?></li>
        </ol>
    </header>

    <article class="product-details">
        <div class="product-details__image">
            <img src="<?= e(asset($product->getImageUrlPath())) ?>" alt="<?= e($product->getName()) ?>" width="600" height="600">
        </div>

        <div class="product-details__info">
            <span class="badge"><?= e($product->getCategoryName()) ?></span>
            <h1><?= e($product->getName()) ?></h1>
            <p class="product-card__brand">Brand: <?= e($product->getBrandName()) ?></p>
            <p class="price price--large"><?= e($product->getFormattedPrice()) ?></p>

            <?php if (!$product->isInStock()): ?>
                <p class="stock-status stock-status--out">Stoc epuizat</p>
            <?php elseif ($product->isLowStock()): ?>
                <p class="stock-status stock-status--low">Doar <?= e($product->getStock()) ?> bucăți în stoc</p>
            <?php else: ?>
                <p class="stock-status stock-status--in">În stoc</p>
            <?php endif; ?>

            <p><?= nl2br(e($product->getDescription())) ?></p>

            <?php if ($product->isInStock()): ?>
                <form class="add-to-cart-form" method="post" action="<?= url('/cart/add') ?>">
                    <input type="hidden" name="productId" value="<?= e($product->getId()) ?>">
                    <label class="visually-hidden" for="quantity">Cantitate</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= e(min($product->getStock(), 20)) ?>">
                    <button class="button" type="submit">Adaugă în coș</button>
                </form>
            <?php endif; ?>

            <div class="table-wrapper">
                <table class="table">
                    <caption>Specificații produs</caption>
                    <tbody>
                    <tr>
                        <th scope="row">Categorie</th>
                        <td><?= e($product->getCategoryName()) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Brand</th>
                        <td><?= e($product->getBrandName()) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Cantitate</th>
                        <td><?= $product->getWeightGrams() !== null ? e($product->getWeightGrams()) . ' g' : '-' ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Porții</th>
                        <td><?= $product->getServings() !== null ? e($product->getServings()) : '-' ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Preț / porție</th>
                        <td><?= $pricePerServing !== null ? e(formatPrice($pricePerServing)) : '-' ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </article>

    <?php if ($relatedProducts !== []): ?>
        <section class="section" aria-labelledby="related-title">
            <h2 id="related-title">Produse similare</h2>
            <div class="product-grid">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <?= App\Core\View::renderFile('partials/productCard', ['product' => $relatedProduct]) ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
