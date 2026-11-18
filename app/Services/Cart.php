<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use InvalidArgumentException;

/**
 * Shopping cart. It stores only "product id => quantity" pairs;
 * prices are always read again from the database, never trusted from the browser.
 *
 * The storage array is received by reference, so the cart can work with $_SESSION
 * in the application and with a plain array in the unit tests.
 */
final class Cart
{
    public const MAX_QUANTITY_PER_PRODUCT = 20;
    private const SESSION_KEY = 'cart';

    /** @var array<int, int> */
    private array $items;

    public function __construct(array &$storage)
    {
        $this->items = &$storage;
    }

    public static function fromSession(): self
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        return new self($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Adds a product (or increases its quantity) and returns the new quantity.
     *
     * @throws InvalidArgumentException when the quantity or the stock is not valid
     */
    public function add(int $productId, int $quantity = 1, ?int $availableStock = null): int
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Cantitatea trebuie să fie cel puțin 1.');
        }

        $limit = $this->getLimit($availableStock);
        if ($limit < 1) {
            throw new InvalidArgumentException('Produsul nu mai este în stoc.');
        }

        $newQuantity = min($this->getQuantity($productId) + $quantity, $limit);
        $this->items[$productId] = $newQuantity;

        return $newQuantity;
    }

    /**
     * Sets the quantity of a product. A quantity of 0 removes the product.
     */
    public function update(int $productId, int $quantity, ?int $availableStock = null): void
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        $limit = $this->getLimit($availableStock);
        if ($limit < 1) {
            $this->remove($productId);
            return;
        }

        $this->items[$productId] = min($quantity, $limit);
    }

    public function remove(int $productId): void
    {
        unset($this->items[$productId]);
    }

    public function clear(): void
    {
        foreach (array_keys($this->items) as $productId) {
            unset($this->items[$productId]);
        }
    }

    public function getQuantity(int $productId): int
    {
        return $this->items[$productId] ?? 0;
    }

    /**
     * @return array<int, int>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return array_keys($this->items);
    }

    public function getTotalQuantity(): int
    {
        return array_sum($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * Combines the cart with the products loaded from the database.
     * Products that no longer exist are removed from the cart.
     *
     * @param array<int, Product> $productsById
     * @return array<int, array{product: Product, quantity: int, subtotal: float}>
     */
    public function getLines(array $productsById): array
    {
        $lines = [];

        foreach ($this->items as $productId => $quantity) {
            if (!isset($productsById[$productId])) {
                $this->remove($productId);
                continue;
            }

            $product = $productsById[$productId];
            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => round($product->getPrice() * $quantity, 2),
            ];
        }

        return $lines;
    }

    /**
     * @param array<int, float> $pricesByProductId
     */
    public function calculateTotal(array $pricesByProductId): float
    {
        $total = 0.0;

        foreach ($this->items as $productId => $quantity) {
            $total += ($pricesByProductId[$productId] ?? 0.0) * $quantity;
        }

        return round($total, 2);
    }

    private function getLimit(?int $availableStock): int
    {
        return $availableStock === null
            ? self::MAX_QUANTITY_PER_PRODUCT
            : min(self::MAX_QUANTITY_PER_PRODUCT, $availableStock);
    }
}
