<?php

namespace App\Repositories;

use App\Interfaces\BagRepository;
use App\Models\Bag;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Pkt\StarterKit\Helpers\DxAdapter;

class BagRepositoryImpl implements BagRepository
{
    public function getBagDataProcessingWith(?array $with = null): Builder
    {
        $data = Bag::query()->with($with)->select('*');
        $loadData = DxAdapter::load($data);
        return $loadData;
    }

    public function createBag(array $bagData): Bag
    {
        DB::beginTransaction();
        try {
            $bag = Bag::query()->create($bagData);
            DB::commit();
            return $bag;
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
            throw new \RuntimeException("Failed to create bag");
        }
    }

    public function updateBag(String $bagId, array $bagData): Bag
    {
        DB::beginTransaction();
        try {
            $bag = Bag::query()->where('id', $bagId)->firstOrFail();
            $bag->update($bagData);
            DB::commit();
            return $bag;
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Bag not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to update bag");
        }
    }

    public function deleteBag(String $bagId): void
    {
        try {
            $bag = Bag::query()->where('id', $bagId)->firstOrFail();
            $bag->delete();
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Bag not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to delete bag");
        }
    }
}
