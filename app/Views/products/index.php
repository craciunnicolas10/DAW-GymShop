<?php
/**
 * @var App\Models\Product[] $products
 * @var App\Models\Category[] $categories
 * @var App\Models\Category|null $activeCategory
 * @var string $search
 * @var string $sort
 */
$sortLabels = [
    'newest' => 'Cele mai noi',
    'price_asc' => 'Preț crescător',
    'price_desc' => 'Preț descrescător',
    'name' => 'Nume (A-Z)',
];
?>
<div class="container">
    <header class="page-header">
        <ol class="breadcrumbs">
            <li><a href="<?= url('/') ?>">Acasă</a></li>
            <li><a href="<?= url('/products') ?>">Produse</a></li>
            <?php if ($activeCategory !== null): ?>
                <li><?= e($activeCategory->getName()) ?></li>
            <?php endif; ?>
        </ol>
        <h1><?= e($activeCategory?->getName() ?? 'Toate produsele') ?></h1>
        <?php if ($activeCategory !== null): ?>
            <p class="text-muted"><?= e($activeCategory->getDescription()) ?></p>
        <?php endif; ?>
    </header>

    <div class="catalog-layout">
        <aside class="filters" aria-label="Filtre produse">
            <h2>Categorii</h2>
            <ul class="category-list">
                <li>
                    <a class="<?= $activeCategory === null ? 'is-active' : '' ?>" href="<?= url('/products') ?>">Toate</a>
                </li>
                <?php foreach ($categories as $category): ?>
                    <li>
                        <a class="<?= $activeCategory?->getId() === $category->getId() ? 'is-active' : '' ?>"
                           href="<?= url('/products?category=' . urlencode($category->getSlug())) ?>">
                            <span><?= e($category->getName()) ?></span>
                            <span><?= e($category->getProductCount()) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form method="get" action="<?= url('/products') ?>">
                <?php if ($activeCategory !== null): ?>
                    <input type="hidden" name="category" value="<?= e($activeCategory->getSlug()) ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="search">Caută</label>
                    <input type="search" id="search" name="search" value="<?= e($search) ?>" placeholder="ex. creatină" maxlength="100">
                </div>
                <div class="form-group">
                    <label for="sort">Sortează</label>
                    <select id="sort" name="sort">
                        <?php foreach ($sortLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $sort === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="button button--dark button--block" type="submit">Aplică filtrele</button>
            </form>
        </aside>

        <section aria-label="Lista de produse">
            <div class="results-bar">
                <span><?= count($products) ?> produse găsite<?= $search !== '' ? ' pentru „' . e($search) . '”' : '' ?></span>
            </div>

            <?php if ($products === []): ?>
                <div class="empty-state">
                    <p>Nu am găsit produse care să corespundă căutării.</p>
                    <a class="button button--outline" href="<?= url('/products') ?>">Vezi toate produsele</a>
                </div>
            <?php else: ?>
                <div class="product-grid product-grid--catalog">
                    <?php foreach ($products as $product): ?>
                        <?= App\Core\View::renderFile('partials/productCard', ['product' => $product]) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
