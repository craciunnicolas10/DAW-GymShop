<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Model;

/**
 * Contract implemented by every repository (the classes that talk to the database).
 * Controllers depend on these methods, not on SQL.
 */
interface RepositoryInterface
{
    /**
     * @return Model[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Model;

    public function deleteById(int $id): bool;
}
