<?php

declare(strict_types=1);

namespace App\Models;

class Order extends Model
{
    public const STATUS_LABELS = [
        'pending' => 'În așteptare',
        'processing' => 'În procesare',
        'shipped' => 'Expediată',
        'delivered' => 'Livrată',
        'cancelled' => 'Anulată',
    ];

    /** @var OrderItem[] */
    private array $items = [];

    public function __construct(
        ?int $id,
        private int $userId,
        private string $status,
        private string $shippingAddress,
        private string $phone,
        private ?string $createdAt = null,
        private float $total = 0.0,
        private ?string $customerName = null,
        private ?string $customerEmail = null
    ) {
        $this->id = $id;
    }

    public static function fromArray(array $row): static
    {
        return new static(
            isset($row['id']) ? (int) $row['id'] : null,
            (int) $row['user_id'],
            (string) $row['status'],
            (string) $row['shipping_address'],
            (string) $row['phone'],
            $row['created_at'] ?? null,
            (float) ($row['total'] ?? 0),
            $row['customer_name'] ?? null,
            $row['customer_email'] ?? null
        );
    }

    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::STATUS_LABELS);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getShippingAddress(): string
    {
        return $this->shippingAddress;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    /**
     * @param OrderItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
        $this->total = array_reduce($items, static fn (float $sum, OrderItem $item): float => $sum + $item->getSubtotal(), 0.0);
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): float
    {
        return round($this->total, 2);
    }

    public function belongsTo(int $userId): bool
    {
        return $this->userId === $userId;
    }
}
