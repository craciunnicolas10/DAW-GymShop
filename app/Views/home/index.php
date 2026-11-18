<?php
/**
 * @var App\Models\Category[] $categories
 * @var App\Models\Product[] $featuredProducts
 */
?>
<section class="container section">
    <div class="section-header">
        <div>
            <h1>Suplimente pentru sală</h1>
            <p class="section-subtitle">Creatină, proteine și tot ce ai nevoie pentru progres, livrate rapid în toată țara.</p>
        </div>
    </div>
</section>

<section class="container section" aria-labelledby="categories-title">
    <div class="section-header">
        <div>
            <h2 id="categories-title">Categorii</h2>
            <p class="section-subtitle">Alege suplimentul potrivit obiectivului tău.</p>
        </div>
        <a href="<?= url('/products') ?>">Vezi toate produsele &rarr;</a>
    </div>
    <div class="category-grid">
        <?php foreach ($categories as $category): ?>
            <a class="category-card" href="<?= url('/products?category=' . urlencode($category->getSlug())) ?>">
                <strong><?= e($category->getName()) ?></strong>
                <span><?= e($category->getProductCount()) ?> produse</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="container section" aria-labelledby="featured-title">
    <div class="section-header">
        <div>
            <h2 id="featured-title">Produse recomandate</h2>
            <p class="section-subtitle">Cele mai căutate suplimente din magazin.</p>
        </div>
    </div>
    <div class="product-grid">
        <?php foreach ($featuredProducts as $product): ?>
            <?= App\Core\View::renderFile('partials/productCard', ['product' => $product]) ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="container section" aria-labelledby="benefits-title">
    <h2 id="benefits-title">De ce GymShop?</h2>
    <ul class="feature-list">
        <li><strong>Livrare 24-48h</strong> Comenzile plasate până la ora 14:00 pleacă în aceeași zi.</li>
        <li><strong>Produse verificate</strong> Lucrăm doar cu distribuitori autorizați și loturi testate.</li>
        <li><strong>Retur 14 zile</strong> Poți returna produsele sigilate fără explicații.</li>
        <li><strong>Consiliere gratuită</strong> Antrenorii noștri te ajută să alegi suplimentul potrivit.</li>
    </ul>
</section>
