<?php

namespace App\Repositories;

use App\Interfaces\WarehouseRepository;
use App\Models\Warehouse;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Pkt\StarterKit\Helpers\DxAdapter;

class WarehouseRepositoryImpl implements WarehouseRepository
{
    public function getWarehouseDataProcessingWith(?array $with = null): Builder
    {
        $data = Warehouse::query()->with($with)->select('*');
        $loadData = DxAdapter::load($data);
        return $loadData;
    }

    public function createWarehouse(array $warehouseData): Warehouse
    {
        DB::beginTransaction();
        try {
            $warehouse = Warehouse::query()->create($warehouseData);
            DB::commit();
            return $warehouse;
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
            throw new \RuntimeException("Failed to create warehouse");
        }
    }

    public function updateWarehouse(String $warehouseId, array $warehouseData): Warehouse
    {
        DB::beginTransaction();
        try {
            $warehouse = Warehouse::query()->where('id', $warehouseId)->firstOrFail();
            $warehouse->update($warehouseData);
            DB::commit();
            return $warehouse;
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Warehouse not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to update warehouse");
        }
    }

    public function deleteWarehouse(String $warehouseId): void
    {
        try {
            $warehouse = Warehouse::query()->where('id', $warehouseId)->firstOrFail();
            $warehouse->delete();
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Warehouse not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to delete warehouse");
        }
    }
}
