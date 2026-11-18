<?php

declare(strict_types=1);

namespace App\Models;

/**
 * One line of an order. It has a composite key (order_id, product_id),
 * so it does not extend Model.
 */
class OrderItem
{
    public function __construct(
        private int $productId,
        private string $productName,
        private int $quantity,
        private float $unitPrice,
        private ?string $productSlug = null
    ) {
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['product_id'],
            (string) $row['product_name'],
            (int) $row['quantity'],
            (float) $row['unit_price'],
            $row['product_slug'] ?? null
        );
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductSlug(): ?string
    {
        return $this->productSlug;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getSubtotal(): float
    {
        return round($this->unitPrice * $this->quantity, 2);
    }
}
