<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Services\Cart;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CartTest extends TestCase
{
    private array $storage;
    private Cart $cart;

    protected function setUp(): void
    {
        $this->storage = [];
        $this->cart = new Cart($this->storage);
    }

    public function testAddNewProductStoresQuantityInStorage(): void
    {
        $newQuantity = $this->cart->add(5, 2);

        $this->assertSame(2, $newQuantity);
        $this->assertSame([5 => 2], $this->storage);
    }

    public function testAddExistingProductIncreasesQuantity(): void
    {
        $this->cart->add(5, 1);
        $this->cart->add(5, 3);

        $this->assertSame(4, $this->cart->getQuantity(5));
    }

    public function testAddIsLimitedByAvailableStock(): void
    {
        $this->assertSame(3, $this->cart->add(5, 10, 3));
    }

    public function testAddIsLimitedByMaximumQuantityPerProduct(): void
    {
        $this->assertSame(Cart::MAX_QUANTITY_PER_PRODUCT, $this->cart->add(5, 50));
    }

    public function testAddThrowsExceptionForInvalidQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cart->add(5, 0);
    }

    public function testAddThrowsExceptionWhenProductIsOutOfStock(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cart->add(5, 1, 0);
    }

    public function testUpdateWithZeroQuantityRemovesProduct(): void
    {
        $this->cart->add(5, 2);
        $this->cart->update(5, 0);

        $this->assertTrue($this->cart->isEmpty());
    }

    public function testUpdateSetsExactQuantity(): void
    {
        $this->cart->add(5, 2);
        $this->cart->update(5, 7, 10);

        $this->assertSame(7, $this->cart->getQuantity(5));
    }

    public function testTotalQuantityAndTotalPrice(): void
    {
        $this->cart->add(1, 2);
        $this->cart->add(2, 1);

        $this->assertSame(3, $this->cart->getTotalQuantity());
        $this->assertEqualsWithDelta(399.97, $this->cart->calculateTotal([1 => 89.99, 2 => 219.99]), 0.001);
    }

    public function testGetLinesRemovesProductsThatNoLongerExist(): void
    {
        $this->cart->add(1, 2);
        $this->cart->add(99, 1);

        $product = new Product(1, 1, 1, 'Creatină', 'creatina', 'Descriere', 89.99, 10);
        $lines = $this->cart->getLines([1 => $product]);

        $this->assertCount(1, $lines);
        $this->assertSame($product, $lines[0]['product']);
        $this->assertEqualsWithDelta(179.98, $lines[0]['subtotal'], 0.001);
        $this->assertSame([1 => 2], $this->storage);
    }

    public function testClearEmptiesTheStorage(): void
    {
        $this->cart->add(1, 2);
        $this->cart->add(2, 1);
        $this->cart->clear();

        $this->assertSame([], $this->storage);
    }
}
