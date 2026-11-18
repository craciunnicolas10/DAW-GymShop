<?php

declare(strict_types=1);

namespace App\Models;

class Category extends Model
{
    public function __construct(
        ?int $id,
        private string $name,
        private string $slug,
        private string $description = '',
        private int $productCount = 0
    ) {
        $this->id = $id;
    }

    public static function fromArray(array $row): static
    {
        return new static(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['name'],
            (string) $row['slug'],
            (string) ($row['description'] ?? ''),
            (int) ($row['product_count'] ?? 0)
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }
}
