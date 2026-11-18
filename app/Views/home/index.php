<?php
/**
 * @var App\Models\Category[] $categories
 * @var App\Models\Product[] $featuredProducts
 */
?>
<?php
$slides = [
    [
        'image' => 'assets/images/banners/banner-creatine.svg',
        'eyebrow' => 'Forță și putere',
        'title' => 'Creatină monohidrat de la 89,99 lei',
        'text' => 'Cel mai studiat supliment pentru performanță. 5 g pe zi, fără încărcare.',
        'link' => '/products?category=creatina',
        'button' => 'Vezi creatina',
    ],
    [
        'image' => 'assets/images/banners/banner-protein.svg',
        'eyebrow' => 'Recuperare rapidă',
        'title' => 'Proteine din zer și vegane',
        'text' => 'Până la 27 g de proteină pe porție, pentru masă musculară și refacere.',
        'link' => '/products?category=proteine',
        'button' => 'Alege proteina',
    ],
    [
        'image' => 'assets/images/banners/banner-preworkout.svg',
        'eyebrow' => 'Energie maximă',
        'title' => 'Pre-workout pentru antrenamente intense',
        'text' => 'Focus, pump și energie, cu sau fără stimulente.',
        'link' => '/products?category=pre-workout',
        'button' => 'Descoperă pre-workout',
    ],
];
?>
<section class="hero" aria-label="Oferte principale">
    <h1 class="visually-hidden">GymShop - suplimente pentru sală</h1>
    <div class="carousel" data-carousel data-interval="5000">
        <div class="carousel__track">
            <?php foreach ($slides as $index => $slide): ?>
                <article class="carousel__slide<?= $index === 0 ? ' is-active' : '' ?>"
                         aria-roledescription="slide" aria-label="<?= $index + 1 ?> din <?= count($slides) ?>"
                         <?= $index === 0 ? '' : 'aria-hidden="true"' ?>>
                    <img class="carousel__image" src="<?= e(asset($slide['image'])) ?>" alt="" width="1200" height="480"
                         <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                    <div class="carousel__caption container">
                        <span class="carousel__eyebrow"><?= e($slide['eyebrow']) ?></span>
                        <h2 class="carousel__title"><?= e($slide['title']) ?></h2>
                        <p class="carousel__text"><?= e($slide['text']) ?></p>
                        <a class="button" href="<?= url($slide['link']) ?>" <?= $index === 0 ? '' : 'tabindex="-1"' ?>><?= e($slide['button']) ?></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <button class="carousel__control carousel__control--prev" type="button" aria-label="Slide-ul anterior">&#10094;</button>
        <button class="carousel__control carousel__control--next" type="button" aria-label="Slide-ul următor">&#10095;</button>

        <div class="carousel__dots" role="group" aria-label="Alege slide-ul">
            <?php foreach ($slides as $index => $slide): ?>
                <button class="carousel__dot<?= $index === 0 ? ' is-active' : '' ?>" type="button"
                        aria-label="Mergi la slide-ul <?= $index + 1 ?>" data-slide="<?= $index ?>"></button>
            <?php endforeach; ?>
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
