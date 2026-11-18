<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Str;
use App\Core\Validator;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ImageUploader;
use PDOException;

/**
 * Product management (create, read, update, delete) for administrators.
 */
final class AdminProductController extends AdminController
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private BrandRepository $brandRepository;
    private ImageUploader $imageUploader;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $pdo = Database::getConnection();
        $this->productRepository = new ProductRepository($pdo);
        $this->categoryRepository = new CategoryRepository($pdo);
        $this->brandRepository = new BrandRepository($pdo);
        $this->imageUploader = ImageUploader::fromConfig();
    }

    public function index(): void
    {
        $this->renderAdmin('admin/products/index', 'products', [
            'pageTitle' => 'Administrare produse',
            'products' => $this->productRepository->findAll(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm(null, [], []);
    }

    public function store(): void
    {
        $data = $this->request->all();

        try {
            $slug = $this->validateProduct($data, null);
            $imagePath = $this->uploadImage();
        } catch (ValidationException $exception) {
            $this->renderForm(null, $data, $exception->getErrors(), 422);
            return;
        }

        $product = $this->buildProduct(null, $data, $slug, $imagePath);
        $this->productRepository->create($product);

        $this->session->flash('success', "Produsul „{$product->getName()}” a fost adăugat.");
        $this->redirect('/admin/products');
    }

    public function edit(string $id): void
    {
        $product = $this->findProductOrFail((int) $id);

        $this->renderForm($product, $this->getFormValues($product), []);
    }

    public function update(string $id): void
    {
        $existingProduct = $this->findProductOrFail((int) $id);
        $data = $this->request->all();

        try {
            $slug = $this->validateProduct($data, $existingProduct->getId());
            $newImagePath = $this->uploadImage();
        } catch (ValidationException $exception) {
            $this->renderForm($existingProduct, $data, $exception->getErrors(), 422);
            return;
        }

        if ($newImagePath !== null) {
            $this->imageUploader->delete($existingProduct->getImagePath());
        }

        $product = $this->buildProduct(
            $existingProduct->getId(),
            $data,
            $slug,
            $newImagePath ?? $existingProduct->getImagePath()
        );
        $this->productRepository->update($product);

        $this->session->flash('success', "Produsul „{$product->getName()}” a fost actualizat.");
        $this->redirect('/admin/products');
    }

    public function delete(string $id): void
    {
        $product = $this->findProductOrFail((int) $id);

        try {
            $this->productRepository->deleteById($product->getId());
            $this->imageUploader->delete($product->getImagePath());
            $this->session->flash('success', "Produsul „{$product->getName()}” a fost șters.");
        } catch (PDOException $exception) {
            // SQLSTATE 23000 = integrity constraint violation (the product appears in orders).
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $this->session->flash(
                'error',
                "Produsul „{$product->getName()}” apare în comenzi și nu poate fi șters. Poți seta stocul la 0."
            );
        }

        $this->redirect('/admin/products');
    }

    private function findProductOrFail(int $id): Product
    {
        return $this->productRepository->findById($id) ?? throw new NotFoundException('Produsul nu există.');
    }

    /**
     * Validates the form and returns the slug generated from the product name.
     *
     * @throws ValidationException
     */
    private function validateProduct(array $data, ?int $productId): string
    {
        $categoryIds = array_map(static fn (Category $category): string => (string) $category->getId(), $this->categoryRepository->findAll());
        $brandIds = array_map(static fn (Brand $brand): string => (string) $brand->getId(), $this->brandRepository->findAll());

        $validator = (new Validator($data))
            ->required('name', 'Nume')
            ->minLength('name', 'Nume', 3)
            ->maxLength('name', 'Nume', 150)
            ->required('categoryId', 'Categorie')
            ->in('categoryId', 'Categorie', $categoryIds)
            ->required('brandId', 'Brand')
            ->in('brandId', 'Brand', $brandIds)
            ->required('description', 'Descriere')
            ->minLength('description', 'Descriere', 20)
            ->maxLength('description', 'Descriere', 5000)
            ->required('price', 'Preț')
            ->number('price', 'Preț', 0.01)
            ->required('stock', 'Stoc')
            ->integer('stock', 'Stoc', 0, 100000)
            ->integer('weightGrams', 'Gramaj', 1, 100000)
            ->integer('servings', 'Porții', 1, 10000);

        $errors = $validator->getErrors();
        $slug = Str::slug($data['name'] ?? '');

        if (!isset($errors['name']) && ($slug === '' || $this->productRepository->slugExists($slug, $productId))) {
            $errors['name'] = 'Există deja un produs cu un nume asemănător.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $slug;
    }

    /**
     * @throws ValidationException
     */
    private function uploadImage(): ?string
    {
        $file = $this->request->file('image');

        return $file === null ? null : $this->imageUploader->upload($file);
    }

    private function buildProduct(?int $id, array $data, string $slug, ?string $imagePath): Product
    {
        return new Product(
            $id,
            (int) $data['categoryId'],
            (int) $data['brandId'],
            $data['name'],
            $slug,
            $data['description'],
            round((float) $data['price'], 2),
            (int) $data['stock'],
            ($data['weightGrams'] ?? '') !== '' ? (int) $data['weightGrams'] : null,
            ($data['servings'] ?? '') !== '' ? (int) $data['servings'] : null,
            $imagePath
        );
    }

    private function getFormValues(Product $product): array
    {
        return [
            'name' => $product->getName(),
            'categoryId' => (string) $product->getCategoryId(),
            'brandId' => (string) $product->getBrandId(),
            'description' => $product->getDescription(),
            'price' => number_format($product->getPrice(), 2, '.', ''),
            'stock' => (string) $product->getStock(),
            'weightGrams' => (string) ($product->getWeightGrams() ?? ''),
            'servings' => (string) ($product->getServings() ?? ''),
        ];
    }

    private function renderForm(?Product $product, array $values, array $errors, int $statusCode = 200): void
    {
        $this->renderAdmin('admin/products/form', 'products', [
            'pageTitle' => $product === null ? 'Produs nou' : 'Editare produs',
            'product' => $product,
            'values' => $values,
            'errors' => $errors,
            'categories' => $this->categoryRepository->findAll(),
            'brands' => $this->brandRepository->findAll(),
            'maxUploadMb' => (int) round($this->getMaxUploadBytes() / 1024 / 1024),
        ], $statusCode);
    }

    private function getMaxUploadBytes(): int
    {
        return (int) Config::get('uploads.maxSizeBytes', 12 * 1024 * 1024);
    }
}
