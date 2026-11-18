<?php
/**
 * @var string $activeSection
 * @var array<int, array{date: string, level: string, user: string, ip: string, action: string, details: string}> $entries
 * @var string[] $levels
 * @var string $level
 * @var string $search
 */
?>
<div class="container">
    <header class="page-header section-header">
        <h1>Jurnal de activitate</h1>
        <a class="button button--dark" href="<?= url('/admin/logs/download') ?>">Descarcă fișierul de log</a>
    </header>

    <?= App\Core\View::renderFile('admin/partials/nav', ['activeSection' => $activeSection]) ?>

    <form class="content-card log-filters" method="get" action="<?= url('/admin/logs') ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="level">Nivel</label>
                <select id="level" name="level">
                    <option value="">Toate</option>
                    <?php foreach ($levels as $levelOption): ?>
                        <option value="<?= e($levelOption) ?>" <?= $level === $levelOption ? 'selected' : '' ?>><?= e($levelOption) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="search">Caută (utilizator, acțiune, IP...)</label>
                <input type="search" id="search" name="search" value="<?= e($search) ?>" maxlength="100">
            </div>
        </div>
        <button class="button button--small" type="submit">Filtrează</button>
    </form>

    <?php if ($entries === []): ?>
        <div class="empty-state">
            <p>Nu există înregistrări pentru filtrele alese.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table table--responsive">
                <caption>Ultimele <?= count($entries) ?> înregistrări (cele mai noi primele)</caption>
                <thead>
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Nivel</th>
                    <th scope="col">Utilizator</th>
                    <th scope="col">IP</th>
                    <th scope="col">Acțiune</th>
                    <th scope="col">Detalii</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td data-label="Data"><?= e($entry['date']) ?></td>
                        <td data-label="Nivel"><span class="log-level log-level--<?= e(strtolower($entry['level'])) ?>"><?= e($entry['level']) ?></span></td>
                        <td data-label="Utilizator"><?= e($entry['user']) ?></td>
                        <td data-label="IP"><?= e($entry['ip']) ?></td>
                        <td data-label="Acțiune"><strong><?= e($entry['action']) ?></strong></td>
                        <td data-label="Detalii" class="log-details"><?= e($entry['details']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
