<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\ValidationException;
use App\Models\Model;
use App\Models\Order;
use App\Models\OrderItem;
use Throwable;

class OrderRepository extends BaseRepository
{
    /** The order total is calculated from order_items, it is not stored (3NF). */
    private const SELECT_WITH_TOTAL =
        'SELECT o.id, o.user_id, o.status, o.shipping_address, o.phone, o.created_at,
                u.full_name AS customer_name, u.email AS customer_email,
                COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total
         FROM orders o
         JOIN users u ON u.id = o.user_id
         LEFT JOIN order_items oi ON oi.order_id = o.id';

    private const GROUP_BY =
        ' GROUP BY o.id, o.user_id, o.status, o.shipping_address, o.phone, o.created_at, u.full_name, u.email';

    protected function getTableName(): string
    {
        return 'orders';
    }

    protected function mapRowToModel(array $row): Model
    {
        return Order::fromArray($row);
    }

    /**
     * @return Order[]
     */
    public function findAll(): array
    {
        $statement = $this->pdo->query(self::SELECT_WITH_TOTAL . self::GROUP_BY . ' ORDER BY o.created_at DESC, o.id DESC');

        return $this->mapRows($statement->fetchAll());
    }

    /**
     * @return Order[]
     */
    public function findByUserId(int $userId): array
    {
        $statement = $this->pdo->prepare(
            self::SELECT_WITH_TOTAL . ' WHERE o.user_id = :userId' . self::GROUP_BY . ' ORDER BY o.created_at DESC, o.id DESC'
        );
        $statement->execute(['userId' => $userId]);

        return $this->mapRows($statement->fetchAll());
    }

    /**
     * Returns the order together with its items.
     */
    public function findById(int $id): ?Order
    {
        $statement = $this->pdo->prepare(self::SELECT_WITH_TOTAL . ' WHERE o.id = :id' . self::GROUP_BY);
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        $order = Order::fromArray($row);
        $order->setItems($this->findItems($id));

        return $order;
    }

    /**
     * @return OrderItem[]
     */
    public function findItems(int $orderId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT oi.product_id, oi.quantity, oi.unit_price, p.name AS product_name, p.slug AS product_slug
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = :orderId
             ORDER BY p.name'
        );
        $statement->execute(['orderId' => $orderId]);

        return array_map(static fn (array $row): OrderItem => OrderItem::fromArray($row), $statement->fetchAll());
    }

    /**
     * Saves the order and its items in a single transaction and decreases the stock.
     * If any product no longer has enough stock, nothing is saved.
     *
     * @param array<int, array{productId: int, productName: string, quantity: int, unitPrice: float}> $lines
     * @throws ValidationException when the stock is insufficient
     */
    public function createOrder(int $userId, string $shippingAddress, string $phone, array $lines): int
    {
        $this->pdo->beginTransaction();

        try {
            $orderStatement = $this->pdo->prepare(
                'INSERT INTO orders (user_id, status, shipping_address, phone) VALUES (:userId, :status, :address, :phone)'
            );
            $orderStatement->execute([
                'userId' => $userId,
                'status' => 'pending',
                'address' => $shippingAddress,
                'phone' => $phone,
            ]);
            $orderId = (int) $this->pdo->lastInsertId();

            $stockStatement = $this->pdo->prepare(
                'UPDATE products SET stock = stock - :quantity WHERE id = :productId AND stock >= :minimumStock'
            );
            $itemStatement = $this->pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                 VALUES (:orderId, :productId, :quantity, :unitPrice)'
            );

            foreach ($lines as $line) {
                $stockStatement->execute([
                    'quantity' => $line['quantity'],
                    'productId' => $line['productId'],
                    'minimumStock' => $line['quantity'],
                ]);

                if ($stockStatement->rowCount() === 0) {
                    throw new ValidationException([
                        'stock' => "Stoc insuficient pentru produsul „{$line['productName']}”.",
                    ]);
                }

                $itemStatement->execute([
                    'orderId' => $orderId,
                    'productId' => $line['productId'],
                    'quantity' => $line['quantity'],
                    'unitPrice' => $line['unitPrice'],
                ]);
            }

            $this->pdo->commit();

            return $orderId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $statement = $this->pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $statement->execute(['status' => $status, 'id' => $orderId]);

        return $statement->rowCount() > 0;
    }

    public function getTotalRevenue(): float
    {
        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(oi.quantity * oi.unit_price), 0)
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             WHERE o.status <> :cancelled'
        );
        $statement->execute(['cancelled' => 'cancelled']);

        return (float) $statement->fetchColumn();
    }
}
