<?php
/**
 * Main layout shared by all pages.
 *
 * @var string $content
 * @var string $appName
 * @var array $flashMessages
 * @var array|null $currentUser
 * @var int $cartCount
 */
$currentUser = $currentUser ?? null;
$cartCount = $cartCount ?? 0;
$fullTitle = isset($pageTitle) ? $pageTitle . ' | ' . $appName : $appName . ' - Suplimente pentru sală';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'GymShop - magazin online de suplimente pentru sală: creatină, proteine, pre-workout, aminoacizi și vitamine, cu livrare rapidă în toată țara.') ?>">
    <link rel="icon" href="<?= asset('assets/images/logo.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/animations.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/responsive.css') ?>">
</head>
<body>
<a class="skip-link" href="#main-content">Sari la conținut</a>

<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="<?= url('/') ?>">
            <img src="<?= asset('assets/images/logo.svg') ?>" alt="" width="36" height="36">
            <span>Gym<strong>Shop</strong></span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Deschide meniul">
            <span></span><span></span><span></span>
        </button>

        <nav id="main-nav" class="main-nav" aria-label="Meniu principal">
            <ul class="nav-links">
                <li><a href="<?= url('/') ?>">Acasă</a></li>
                <li><a href="<?= url('/products') ?>">Produse</a></li>
                <li><a href="<?= url('/products?category=creatina') ?>">Creatină</a></li>
                <li><a href="<?= url('/products?category=proteine') ?>">Proteine</a></li>
                <li><a href="<?= url('/about') ?>">Ghid suplimente</a></li>
            </ul>

            <div class="nav-actions">
                <a class="cart-link" href="<?= url('/cart') ?>" aria-label="Coșul de cumpărături, <?= e($cartCount) ?> produse">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM5.2 4l.6 3H21l-2 8H7.6L4.4 2H1v2h3.4l.4 2Z"/></svg>
                    <span>Coș</span>
                    <span class="cart-badge<?= $cartCount > 0 ? ' is-visible' : '' ?>"><?= e($cartCount) ?></span>
                </a>

                <?php if ($currentUser !== null): ?>
                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                        <a class="nav-button nav-button--accent" href="<?= url('/admin') ?>">Admin</a>
                    <?php endif; ?>
                    <a class="nav-button" href="<?= url('/orders') ?>">Comenzile mele</a>
                    <form class="inline-form" method="post" action="<?= url('/logout') ?>">
                        <button class="nav-button nav-button--ghost" type="submit">Ieșire (<?= e($currentUser['name']) ?>)</button>
                    </form>
                <?php else: ?>
                    <a class="nav-button" href="<?= url('/login') ?>">Autentificare</a>
                    <a class="nav-button nav-button--accent" href="<?= url('/register') ?>">Cont nou</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main id="main-content">
    <?php if (!empty($flashMessages)): ?>
        <div class="container flash-container">
            <?php foreach ($flashMessages as $flash): ?>
                <div class="alert alert--<?= e($flash['type']) ?>" role="status">
                    <span><?= e($flash['message']) ?></span>
                    <button class="alert-close" type="button" aria-label="Închide mesajul">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <p class="footer-logo">Gym<strong>Shop</strong></p>
            <p>Suplimente testate pentru forță, masă musculară și recuperare. Livrare în 24-48h în toată România.</p>
        </div>
        <div>
            <h2 class="footer-title">Magazin</h2>
            <ul class="footer-links">
                <li><a href="<?= url('/products?category=creatina') ?>">Creatină</a></li>
                <li><a href="<?= url('/products?category=proteine') ?>">Proteine</a></li>
                <li><a href="<?= url('/products?category=pre-workout') ?>">Pre-Workout</a></li>
                <li><a href="<?= url('/products?category=vitamine-si-minerale') ?>">Vitamine și minerale</a></li>
            </ul>
        </div>
        <div>
            <h2 class="footer-title">Informații</h2>
            <ul class="footer-links">
                <li><a href="<?= url('/about') ?>">Ghid suplimente</a></li>
                <li><a href="https://anpc.ro/" target="_blank" rel="noopener noreferrer">ANPC</a></li>
                <li><a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer">Soluționarea online a litigiilor</a></li>
            </ul>
        </div>
        <div>
            <h2 class="footer-title">Contact</h2>
            <ul class="footer-links">
                <li><a href="mailto:contact@gymshop.test">contact@gymshop.test</a></li>
                <li><a href="tel:+40700000000">0700 000 000</a></li>
                <li>Luni - Vineri, 09:00 - 18:00</li>
            </ul>
        </div>
    </div>
    <p class="container footer-copyright">&copy; <?= date('Y') ?> GymShop. Proiect realizat pentru disciplina Dezvoltarea Aplicațiilor Web.</p>
</footer>

<script src="<?= asset('assets/js/main.js') ?>" defer></script>
<?php foreach ($scripts ?? [] as $script): ?>
    <script src="<?= asset($script) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
