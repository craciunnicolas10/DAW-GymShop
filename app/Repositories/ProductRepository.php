<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Model;
use App\Models\Product;
use PDO;

class ProductRepository extends BaseRepository
{
    /** Allowed sort options => ORDER BY clause (whitelist, never built from user input). */
    public const SORT_OPTIONS = [
        'newest' => 'p.created_at DESC, p.id DESC',
        'price_asc' => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'name' => 'p.name ASC',
    ];

    private const SELECT_WITH_RELATIONS =
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug, b.name AS brand_name
         FROM products p
         JOIN categories c ON c.id = p.category_id
         JOIN brands b ON b.id = p.brand_id';

    protected function getTableName(): string
    {
        return 'products';
    }

    protected function mapRowToModel(array $row): Model
    {
        return Product::fromArray($row);
    }

    /**
     * @return Product[]
     */
    public function findAll(): array
    {
        $statement = $this->pdo->query(self::SELECT_WITH_RELATIONS . ' ORDER BY p.id DESC');

        return $this->mapRows($statement->fetchAll());
    }

    public function findById(int $id): ?Product
    {
        $statement = $this->pdo->prepare(self::SELECT_WITH_RELATIONS . ' WHERE p.id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Product::fromArray($row);
    }

    public function findBySlug(string $slug): ?Product
    {
        $statement = $this->pdo->prepare(self::SELECT_WITH_RELATIONS . ' WHERE p.slug = :slug');
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        return $row === false ? null : Product::fromArray($row);
    }

    /**
     * Product list filtered by category and searched by product or brand name.
     *
     * @return Product[]
     */
    public function findByFilters(?string $categorySlug, string $search, string $sort): array
    {
        $conditions = [];
        $parameters = [];

        if ($categorySlug !== null && $categorySlug !== '') {
            $conditions[] = 'c.slug = :categorySlug';
            $parameters['categorySlug'] = $categorySlug;
        }

        if ($search !== '') {
            $conditions[] = '(p.name LIKE :searchName OR b.name LIKE :searchBrand)';
            $parameters['searchName'] = '%' . $search . '%';
            $parameters['searchBrand'] = '%' . $search . '%';
        }

        $sql = self::SELECT_WITH_RELATIONS;
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY ' . (self::SORT_OPTIONS[$sort] ?? self::SORT_OPTIONS['newest']);

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return $this->mapRows($statement->fetchAll());
    }

    /**
     * @return Product[]
     */
    public function findFeatured(int $limit = 4): array
    {
        $statement = $this->pdo->prepare(
            self::SELECT_WITH_RELATIONS . ' WHERE p.stock > 0 ORDER BY p.created_at DESC, p.id ASC LIMIT :limit'
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $this->mapRows($statement->fetchAll());
    }

    /**
     * Loads several products at once (used by the shopping cart).
     *
     * @param int[] $ids
     * @return array<int, Product> product id => product
     */
    public function findByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $statement = $this->pdo->prepare(self::SELECT_WITH_RELATIONS . " WHERE p.id IN ({$placeholders})");
        $statement->execute($ids);

        $products = [];
        foreach ($statement->fetchAll() as $row) {
            $product = Product::fromArray($row);
            $products[$product->getId()] = $product;
        }

        return $products;
    }

    public function create(Product $product): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO products (category_id, brand_id, name, slug, description, price, stock, weight_grams, servings, image_path)
             VALUES (:categoryId, :brandId, :name, :slug, :description, :price, :stock, :weightGrams, :servings, :imagePath)'
        );
        $statement->execute($this->toParameters($product));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Product $product): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE products
             SET category_id = :categoryId, brand_id = :brandId, name = :name, slug = :slug, description = :description,
                 price = :price, stock = :stock, weight_grams = :weightGrams, servings = :servings, image_path = :imagePath
             WHERE id = :id'
        );
        $statement->execute($this->toParameters($product) + ['id' => $product->getId()]);
    }

    public function slugExists(string $slug, ?int $exceptProductId = null): bool
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM products WHERE slug = :slug AND id <> :exceptId');
        $statement->execute(['slug' => $slug, 'exceptId' => $exceptProductId ?? 0]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function countLowStock(): int
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM products WHERE stock <= :limit');
        $statement->execute(['limit' => Product::LOW_STOCK_LIMIT]);

        return (int) $statement->fetchColumn();
    }

    private function toParameters(Product $product): array
    {
        return [
            'categoryId' => $product->getCategoryId(),
            'brandId' => $product->getBrandId(),
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'stock' => $product->getStock(),
            'weightGrams' => $product->getWeightGrams(),
            'servings' => $product->getServings(),
            'imagePath' => $product->getImagePath(),
        ];
    }
}
