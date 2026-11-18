<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Model;
use PDO;

/**
 * Generic implementation of the common CRUD queries.
 * Subclasses only say which table they use and how a row becomes a model.
 */
abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(protected PDO $pdo)
    {
    }

    abstract protected function getTableName(): string;

    abstract protected function mapRowToModel(array $row): Model;

    public function findAll(): array
    {
        $statement = $this->pdo->query("SELECT * FROM {$this->getTableName()} ORDER BY id");

        return $this->mapRows($statement->fetchAll());
    }

    public function findById(int $id): ?Model
    {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE id = :id");
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToModel($row);
    }

    public function deleteById(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->getTableName()} WHERE id = :id");
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$this->getTableName()}")->fetchColumn();
    }

    /**
     * @return Model[]
     */
    protected function mapRows(array $rows): array
    {
        return array_map(fn (array $row): Model => $this->mapRowToModel($row), $rows);
    }
}
