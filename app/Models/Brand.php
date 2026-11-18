<?php

declare(strict_types=1);

namespace App\Models;

class Brand extends Model
{
    public function __construct(?int $id, private string $name, private string $country)
    {
        $this->id = $id;
    }

    public static function fromArray(array $row): static
    {
        return new static(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['name'],
            (string) $row['country']
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
