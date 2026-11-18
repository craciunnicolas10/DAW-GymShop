<?php
/**
 * @var array<string, string> $errors
 * @var array<string, string> $old
 */
$fields = [
    'fullName' => ['label' => 'Nume complet', 'type' => 'text', 'autocomplete' => 'name'],
    'email' => ['label' => 'Email', 'type' => 'email', 'autocomplete' => 'email'],
    'password' => ['label' => 'Parolă', 'type' => 'password', 'autocomplete' => 'new-password'],
    'passwordConfirmation' => ['label' => 'Confirmă parola', 'type' => 'password', 'autocomplete' => 'new-password'],
];
?>
<section class="container section">
    <div class="form-card">
        <h1>Cont nou</h1>
        <p class="text-muted">Creează un cont gratuit pentru a comanda suplimentele preferate.</p>

        <form method="post" action="<?= url('/register') ?>" novalidate>
            <?php foreach ($fields as $name => $field): ?>
                <div class="form-group<?= isset($errors[$name]) ? ' has-error' : '' ?>">
                    <label for="<?= e($name) ?>"><?= e($field['label']) ?></label>
                    <input type="<?= e($field['type']) ?>" id="<?= e($name) ?>" name="<?= e($name) ?>"
                           value="<?= $field['type'] === 'password' ? '' : e($old[$name] ?? '') ?>"
                           autocomplete="<?= e($field['autocomplete']) ?>" required>
                    <?php if ($name === 'password'): ?>
                        <span class="form-hint">Minimum 8 caractere, cel puțin o literă și o cifră.</span>
                    <?php endif; ?>
                    <?php if (isset($errors[$name])): ?>
                        <span class="form-error"><?= e($errors[$name]) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button class="button button--block" type="submit">Creează contul</button>
        </form>

        <p class="form-footer">Ai deja cont? <a href="<?= url('/login') ?>">Autentifică-te</a></p>
    </div>
</section>
