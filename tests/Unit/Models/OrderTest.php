<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function testTotalIsCalculatedFromItems(): void
    {
        $order = new Order(1, 2, 'pending', 'Str. Exemplu 10, București', '0712345678');
        $order->setItems([
            new OrderItem(1, 'Creatină', 2, 89.99),
            new OrderItem(4, 'Whey', 1, 219.99),
        ]);

        $this->assertCount(2, $order->getItems());
        $this->assertEqualsWithDelta(399.97, $order->getTotal(), 0.001);
    }

    public function testOrderItemSubtotal(): void
    {
        $this->assertEqualsWithDelta(359.97, (new OrderItem(1, 'Creatină', 3, 119.99))->getSubtotal(), 0.001);
    }

    public function testStatusLabelsAndValidation(): void
    {
        $order = new Order(1, 2, 'shipped', 'Adresă', '0712345678');

        $this->assertSame('Expediată', $order->getStatusLabel());
        $this->assertTrue(Order::isValidStatus('delivered'));
        $this->assertFalse(Order::isValidStatus('lost'));
    }

    public function testBelongsToChecksTheOwner(): void
    {
        $order = new Order(1, 2, 'pending', 'Adresă', '0712345678');

        $this->assertTrue($order->belongsTo(2));
        $this->assertFalse($order->belongsTo(3));
    }
}
