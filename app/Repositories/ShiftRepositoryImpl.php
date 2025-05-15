<?php

namespace App\Repositories;

use App\Interfaces\ShiftRepository;
use App\Models\Shift;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Pkt\StarterKit\Helpers\DxAdapter;

class ShiftRepositoryImpl implements ShiftRepository
{
    public function getShiftDataProcessingWith(?array $with = null): Builder
    {
        $data = Shift::query()->with($with)->select('*');
        $loadData = DxAdapter::load($data);
        return $loadData;
    }

    public function createShift(array $shiftData): Shift
    {
        DB::beginTransaction();
        try {
            $shiftData = Shift::query()->create($shiftData);
            DB::commit();
            return $shiftData;
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
            throw new \RuntimeException("Failed to create shift");
        }
    }

    public function updateShift(String $shiftId, array $shiftData): Shift
    {
        DB::beginTransaction();
        try {
            $shift = Shift::query()->where('id', $shiftId)->firstOrFail();
            $shift->update($shiftData);
            DB::commit();
            return $shift;
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Shift not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to update shift");
        }
    }

    public function deleteShift(String $shiftId): void
    {
        try {
            $shift = Shift::query()->where('id', $shiftId)->firstOrFail();
            $shift->delete();
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Shift not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to delete shift");
        }
    }
}
