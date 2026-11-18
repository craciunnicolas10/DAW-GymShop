<?php
/**
 * @var string $activeSection
 * @var App\Models\Product[] $products
 */
?>
<div class="container">
    <header class="page-header section-header">
        <h1>Produse</h1>
        <a class="button" href="<?= url('/admin/products/create') ?>">+ Adaugă produs</a>
    </header>

    <?= App\Core\View::renderFile('admin/partials/nav', ['activeSection' => $activeSection]) ?>

    <div class="table-wrapper">
        <table class="table table--responsive">
            <caption><?= count($products) ?> produse în catalog</caption>
            <thead>
            <tr>
                <th scope="col">Produs</th>
                <th scope="col">Categorie</th>
                <th scope="col">Brand</th>
                <th scope="col" class="numeric">Preț</th>
                <th scope="col" class="numeric">Stoc</th>
                <th scope="col">Acțiuni</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td data-label="Produs">
                        <a class="table-product" href="<?= url('/products/' . $product->getSlug()) ?>">
                            <img src="<?= e(asset($product->getImageUrlPath())) ?>" alt="" width="56" height="56" loading="lazy">
                            <span><?= e($product->getName()) ?></span>
                        </a>
                    </td>
                    <td data-label="Categorie"><?= e($product->getCategoryName()) ?></td>
                    <td data-label="Brand"><?= e($product->getBrandName()) ?></td>
                    <td data-label="Preț" class="numeric"><?= e($product->getFormattedPrice()) ?></td>
                    <td data-label="Stoc" class="numeric">
                        <?php if (!$product->isInStock()): ?>
                            <span class="badge badge--danger">0</span>
                        <?php elseif ($product->isLowStock()): ?>
                            <span class="badge badge--warning"><?= e($product->getStock()) ?></span>
                        <?php else: ?>
                            <?= e($product->getStock()) ?>
                        <?php endif; ?>
                    </td>
                    <td data-label="Acțiuni">
                        <div class="table-actions">
                            <a class="button button--small button--outline" href="<?= url('/admin/products/' . $product->getId() . '/edit') ?>">Editează</a>
                            <form method="post" action="<?= url('/admin/products/' . $product->getId() . '/delete') ?>"
                                  data-confirm="Sigur vrei să ștergi produsul „<?= e($product->getName()) ?>”?">
                                <button class="button button--small button--danger" type="submit">Șterge</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
