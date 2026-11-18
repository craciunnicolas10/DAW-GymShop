<?php
/**
 * @var array<string, string> $errors
 * @var array<string, string> $old
 */
?>
<section class="container section">
    <div class="form-card">
        <h1>Autentificare</h1>
        <p class="text-muted">Intră în cont pentru a plasa comenzi și a vedea istoricul lor.</p>

        <form method="post" action="<?= url('/login') ?>" novalidate>
            <div class="form-group<?= isset($errors['email']) ? ' has-error' : '' ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="form-error"><?= e($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group<?= isset($errors['password']) ? ' has-error' : '' ?>">
                <label for="password">Parolă</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="form-error"><?= e($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <button class="button button--block" type="submit">Intră în cont</button>
        </form>

        <p class="form-footer">Nu ai cont? <a href="<?= url('/register') ?>">Creează unul acum</a></p>
    </div>
</section>
