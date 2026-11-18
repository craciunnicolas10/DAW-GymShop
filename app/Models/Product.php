<?php

declare(strict_types=1);

namespace App\Models;

class Product extends Model
{
    public const LOW_STOCK_LIMIT = 5;

    public function __construct(
        ?int $id,
        private int $categoryId,
        private int $brandId,
        private string $name,
        private string $slug,
        private string $description,
        private float $price,
        private int $stock,
        private ?int $weightGrams = null,
        private ?int $servings = null,
        private ?string $imagePath = null,
        private ?string $categoryName = null,
        private ?string $categorySlug = null,
        private ?string $brandName = null
    ) {
        $this->id = $id;
    }

    public static function fromArray(array $row): static
    {
        return new static(
            isset($row['id']) ? (int) $row['id'] : null,
            (int) $row['category_id'],
            (int) $row['brand_id'],
            (string) $row['name'],
            (string) $row['slug'],
            (string) $row['description'],
            (float) $row['price'],
            (int) $row['stock'],
            isset($row['weight_grams']) ? (int) $row['weight_grams'] : null,
            isset($row['servings']) ? (int) $row['servings'] : null,
            $row['image_path'] ?? null,
            $row['category_name'] ?? null,
            $row['category_slug'] ?? null,
            $row['brand_name'] ?? null
        );
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getBrandId(): int
    {
        return $this->brandId;
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getWeightGrams(): ?int
    {
        return $this->weightGrams;
    }

    public function getServings(): ?int
    {
        return $this->servings;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function getCategorySlug(): ?string
    {
        return $this->categorySlug;
    }

    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= self::LOW_STOCK_LIMIT;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2, ',', '.') . ' lei';
    }

    /**
     * Price of a single serving, useful to compare products of different sizes.
     */
    public function getPricePerServing(): ?float
    {
        if ($this->servings === null || $this->servings === 0) {
            return null;
        }

        return round($this->price / $this->servings, 2);
    }

    public function getImageUrlPath(): string
    {
        return $this->imagePath ?: 'assets/images/products/placeholder.svg';
    }
}
