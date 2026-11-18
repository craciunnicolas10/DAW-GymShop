<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';

    public function __construct(
        ?int $id,
        private string $fullName,
        private string $email,
        private string $passwordHash,
        private string $role = self::ROLE_CUSTOMER,
        private ?string $createdAt = null
    ) {
        $this->id = $id;
    }

    public static function fromArray(array $row): static
    {
        return new static(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['full_name'],
            (string) $row['email'],
            (string) $row['password_hash'],
            (string) ($row['role_name'] ?? self::ROLE_CUSTOMER),
            $row['created_at'] ?? null
        );
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }
}
