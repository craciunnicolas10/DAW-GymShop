<?php
/**
 * @var int $statusCode
 * @var string $message
 * @var string|null $technicalDetails
 */
?>
<section class="container section error-page">
    <p class="error-code"><?= e($statusCode) ?></p>
    <h1><?= $statusCode === 404 ? 'Pagina nu a fost găsită' : ($statusCode === 403 ? 'Acces interzis' : 'Ceva nu a mers bine') ?></h1>
    <p><?= e($message) ?></p>
    <?php if (!empty($technicalDetails)): ?>
        <pre class="error-details"><?= e($technicalDetails) ?></pre>
    <?php endif; ?>
    <a class="button" href="<?= url('/') ?>">Înapoi la pagina principală</a>
</section>
