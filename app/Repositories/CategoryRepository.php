<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\Model;

class CategoryRepository extends BaseRepository
{
    protected function getTableName(): string
    {
        return 'categories';
    }

    protected function mapRowToModel(array $row): Model
    {
        return Category::fromArray($row);
    }

    /**
     * Categories ordered by name, together with the number of products in each one.
     *
     * @return Category[]
     */
    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id, c.name, c.slug, c.description
             ORDER BY c.name'
        );

        return $this->mapRows($statement->fetchAll());
    }

    public function findBySlug(string $slug): ?Category
    {
        $statement = $this->pdo->prepare('SELECT * FROM categories WHERE slug = :slug');
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        return $row === false ? null : Category::fromArray($row);
    }
}
