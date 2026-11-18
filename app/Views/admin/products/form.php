<?php
/**
 * Form used both for creating and for editing a product.
 *
 * @var string $activeSection
 * @var App\Models\Product|null $product
 * @var array<string, string> $values
 * @var array<string, string> $errors
 * @var App\Models\Category[] $categories
 * @var App\Models\Brand[] $brands
 * @var int $maxUploadMb
 */
$isEdit = $product !== null;
$action = $isEdit ? url('/admin/products/' . $product->getId() . '/update') : url('/admin/products');
$value = static fn (string $field): string => $values[$field] ?? '';
$errorFor = static fn (string $field): string => isset($errors[$field])
    ? '<span class="form-error">' . e($errors[$field]) . '</span>'
    : '';
$groupClass = static fn (string $field): string => 'form-group' . (isset($errors[$field]) ? ' has-error' : '');
?>
<div class="container">
    <header class="page-header">
        <ol class="breadcrumbs">
            <li><a href="<?= url('/admin') ?>">Admin</a></li>
            <li><a href="<?= url('/admin/products') ?>">Produse</a></li>
            <li><?= $isEdit ? e($product->getName()) : 'Produs nou' ?></li>
        </ol>
        <h1><?= $isEdit ? 'Editare produs' : 'Produs nou' ?></h1>
    </header>

    <?= App\Core\View::renderFile('admin/partials/nav', ['activeSection' => $activeSection]) ?>

    <form class="form-card form-card--wide" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" novalidate>
        <div class="<?= $groupClass('name') ?>">
            <label for="name">Nume produs</label>
            <input type="text" id="name" name="name" value="<?= e($value('name')) ?>" maxlength="150" required>
            <?= $errorFor('name') ?>
        </div>

        <div class="form-row">
            <div class="<?= $groupClass('categoryId') ?>">
                <label for="categoryId">Categorie</label>
                <select id="categoryId" name="categoryId" required>
                    <option value="">Alege categoria</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e($category->getId()) ?>" <?= $value('categoryId') === (string) $category->getId() ? 'selected' : '' ?>>
                            <?= e($category->getName()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= $errorFor('categoryId') ?>
            </div>

            <div class="<?= $groupClass('brandId') ?>">
                <label for="brandId">Brand</label>
                <select id="brandId" name="brandId" required>
                    <option value="">Alege brandul</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= e($brand->getId()) ?>" <?= $value('brandId') === (string) $brand->getId() ? 'selected' : '' ?>>
                            <?= e($brand->getName()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= $errorFor('brandId') ?>
            </div>
        </div>

        <div class="<?= $groupClass('description') ?>">
            <label for="description">Descriere</label>
            <textarea id="description" name="description" maxlength="5000" required><?= e($value('description')) ?></textarea>
            <?= $errorFor('description') ?>
        </div>

        <div class="form-row">
            <div class="<?= $groupClass('price') ?>">
                <label for="price">Preț (lei)</label>
                <input type="number" id="price" name="price" value="<?= e($value('price')) ?>" min="0.01" step="0.01" required>
                <?= $errorFor('price') ?>
            </div>

            <div class="<?= $groupClass('stock') ?>">
                <label for="stock">Stoc (bucăți)</label>
                <input type="number" id="stock" name="stock" value="<?= e($value('stock')) ?>" min="0" step="1" required>
                <?= $errorFor('stock') ?>
            </div>
        </div>

        <div class="form-row">
            <div class="<?= $groupClass('weightGrams') ?>">
                <label for="weightGrams">Gramaj (g) <span class="text-muted">- opțional</span></label>
                <input type="number" id="weightGrams" name="weightGrams" value="<?= e($value('weightGrams')) ?>" min="1" step="1">
                <?= $errorFor('weightGrams') ?>
            </div>

            <div class="<?= $groupClass('servings') ?>">
                <label for="servings">Porții <span class="text-muted">- opțional</span></label>
                <input type="number" id="servings" name="servings" value="<?= e($value('servings')) ?>" min="1" step="1">
                <?= $errorFor('servings') ?>
            </div>
        </div>

        <div class="<?= $groupClass('image') ?>">
            <label for="image">Imagine produs</label>
            <?php if ($isEdit): ?>
                <img class="form-image-preview" src="<?= e(asset($product->getImageUrlPath())) ?>" alt="Imaginea actuală" width="120" height="120">
            <?php endif; ?>
            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
            <span class="form-hint">Formate acceptate: JPG, PNG, WEBP. Dimensiune maximă: <?= e($maxUploadMb) ?> MB.<?= $isEdit ? ' Lasă gol pentru a păstra imaginea actuală.' : '' ?></span>
            <?= $errorFor('image') ?>
        </div>

        <div class="table-actions">
            <button class="button" type="submit"><?= $isEdit ? 'Salvează modificările' : 'Adaugă produsul' ?></button>
            <a class="button button--outline" href="<?= url('/admin/products') ?>">Renunță</a>
        </div>
    </form>
</div>
