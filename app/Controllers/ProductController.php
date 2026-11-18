<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

final class ProductController extends Controller
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $pdo = Database::getConnection();
        $this->productRepository = new ProductRepository($pdo);
        $this->categoryRepository = new CategoryRepository($pdo);
    }

    /**
     * Product catalog with category filter, search and sorting.
     */
    public function index(): void
    {
        $categorySlug = $this->request->query('category');
        $search = mb_substr($this->request->query('search'), 0, 100);
        $sort = $this->request->query('sort', 'newest');

        if (!array_key_exists($sort, ProductRepository::SORT_OPTIONS)) {
            $sort = 'newest';
        }

        $activeCategory = $categorySlug !== '' ? $this->categoryRepository->findBySlug($categorySlug) : null;
        if ($categorySlug !== '' && $activeCategory === null) {
            throw new NotFoundException('Categoria căutată nu există.');
        }

        $this->render('products/index', [
            'pageTitle' => $activeCategory?->getName() ?? 'Toate produsele',
            'metaDescription' => $activeCategory?->getDescription()
                ?? 'Descoperă toate suplimentele GymShop: creatină, proteine, pre-workout, aminoacizi și vitamine.',
            'products' => $this->productRepository->findByFilters($activeCategory?->getSlug(), $search, $sort),
            'categories' => $this->categoryRepository->findAll(),
            'activeCategory' => $activeCategory,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    /**
     * Product details page.
     */
    public function show(string $slug): void
    {
        $product = $this->productRepository->findBySlug($slug);

        if ($product === null) {
            throw new NotFoundException('Produsul căutat nu există.');
        }

        $relatedProducts = array_filter(
            $this->productRepository->findByFilters($product->getCategorySlug(), '', 'newest'),
            static fn (Product $related): bool => $related->getId() !== $product->getId()
        );

        $this->render('products/show', [
            'pageTitle' => $product->getName(),
            'metaDescription' => mb_substr($product->getDescription(), 0, 155),
            'product' => $product,
            'relatedProducts' => array_slice($relatedProducts, 0, 4),
        ]);
    }
}
