<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Model;
use App\Models\Product;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    private function createProduct(float $price = 89.99, int $stock = 50, ?int $servings = 60, ?string $imagePath = null): Product
    {
        return new Product(1, 1, 1, 'Creatină Monohidrat 300g', 'creatina-monohidrat-300g', 'Descriere', $price, $stock, 300, $servings, $imagePath);
    }

    public function testFromArrayMapsDatabaseRow(): void
    {
        $product = Product::fromArray([
            'id' => '4',
            'category_id' => '2',
            'brand_id' => '1',
            'name' => 'Whey Protein',
            'slug' => 'whey-protein',
            'description' => 'Proteină din zer',
            'price' => '219.99',
            'stock' => '40',
            'weight_grams' => '2000',
            'servings' => null,
            'image_path' => 'assets/images/products/whey.svg',
            'category_name' => 'Proteine',
            'category_slug' => 'proteine',
            'brand_name' => 'IronCore Nutrition',
        ]);

        $this->assertInstanceOf(Model::class, $product);
        $this->assertSame(4, $product->getId());
        $this->assertSame(219.99, $product->getPrice());
        $this->assertSame(40, $product->getStock());
        $this->assertNull($product->getServings());
        $this->assertSame('Proteine', $product->getCategoryName());
        $this->assertSame('IronCore Nutrition', $product->getBrandName());
    }

    public function testFormattedPriceUsesRomanianFormat(): void
    {
        $this->assertSame('1.234,50 lei', $this->createProduct(1234.5)->getFormattedPrice());
    }

    public function testStockStatus(): void
    {
        $this->assertTrue($this->createProduct(stock: 50)->isInStock());
        $this->assertFalse($this->createProduct(stock: 50)->isLowStock());
        $this->assertTrue($this->createProduct(stock: 3)->isLowStock());
        $this->assertFalse($this->createProduct(stock: 0)->isInStock());
        $this->assertFalse($this->createProduct(stock: 0)->isLowStock());
    }

    public function testPricePerServing(): void
    {
        $this->assertSame(1.5, $this->createProduct(89.99, servings: 60)->getPricePerServing());
        $this->assertNull($this->createProduct(servings: null)->getPricePerServing());
    }

    public function testImageFallsBackToPlaceholder(): void
    {
        $this->assertSame('assets/images/products/placeholder.svg', $this->createProduct()->getImageUrlPath());
        $this->assertSame('uploads/products/a.png', $this->createProduct(imagePath: 'uploads/products/a.png')->getImageUrlPath());
    }
}
