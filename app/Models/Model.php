<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Base class for all entities stored in the database.
 * Every model has an id and knows how to build itself from a database row.
 */
abstract class Model
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Builds the model from an associative array (a row returned by PDO).
     */
    abstract public static function fromArray(array $row): static;
}
