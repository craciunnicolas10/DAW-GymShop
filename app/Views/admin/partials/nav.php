<?php
/**
 * @var string $activeSection
 */
$sections = [
    'dashboard' => ['label' => 'Panou', 'path' => '/admin'],
    'products' => ['label' => 'Produse', 'path' => '/admin/products'],
    'orders' => ['label' => 'Comenzi', 'path' => '/admin/orders'],
];
?>
<nav class="admin-nav" aria-label="Meniu administrare">
    <?php foreach ($sections as $key => $section): ?>
        <a class="<?= $activeSection === $key ? 'is-active' : '' ?>" href="<?= url($section['path']) ?>"><?= e($section['label']) ?></a>
    <?php endforeach; ?>
</nav>
