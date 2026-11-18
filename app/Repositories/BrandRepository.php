<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Brand;
use App\Models\Model;

class BrandRepository extends BaseRepository
{
    protected function getTableName(): string
    {
        return 'brands';
    }

    protected function mapRowToModel(array $row): Model
    {
        return Brand::fromArray($row);
    }

    /**
     * @return Brand[]
     */
    public function findAll(): array
    {
        return $this->mapRows($this->pdo->query('SELECT * FROM brands ORDER BY name')->fetchAll());
    }
}
