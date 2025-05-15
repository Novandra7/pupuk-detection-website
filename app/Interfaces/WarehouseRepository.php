<?php

namespace App\Interfaces;

use App\Models\Warehouse;
use Illuminate\Contracts\Database\Query\Builder;

interface WarehouseRepository
{
    public function getWarehouseDataProcessingWith(array $with = null): Builder;
    public function createWarehouse(array $warehouseData): Warehouse;
    public function updateWarehouse(String $warehouseId, array $warehouseData): Warehouse;
    public function deleteWarehouse(String $warehouseId): void;
}
