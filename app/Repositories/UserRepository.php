<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Model;
use App\Models\User;

class UserRepository extends BaseRepository
{
    private const SELECT_WITH_ROLE = 'SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id';

    protected function getTableName(): string
    {
        return 'users';
    }

    protected function mapRowToModel(array $row): Model
    {
        return User::fromArray($row);
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : User::fromArray($row);
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.email = :email');
        $statement->execute(['email' => mb_strtolower($email)]);
        $row = $statement->fetch();

        return $row === false ? null : User::fromArray($row);
    }

    public function emailExists(string $email): bool
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $statement->execute(['email' => mb_strtolower($email)]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * Saves a new user and returns the generated id.
     */
    public function create(string $fullName, string $email, string $passwordHash, string $roleName = User::ROLE_CUSTOMER): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (role_id, full_name, email, password_hash)
             VALUES ((SELECT id FROM roles WHERE name = :role_name), :full_name, :email, :password_hash)'
        );
        $statement->execute([
            'role_name' => $roleName,
            'full_name' => $fullName,
            'email' => mb_strtolower($email),
            'password_hash' => $passwordHash,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
