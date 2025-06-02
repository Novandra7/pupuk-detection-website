<?php

namespace App\Repositories;

use App\Interfaces\CctvRepository;
use App\Models\Cctv;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOption\None;
use Pkt\StarterKit\Helpers\DxAdapter;

class CctvRepositoryImpl implements CctvRepository
{
    public function getCctvDataProcessingWith(?array $with = null): Builder
    {
        $data = Cctv::query()->with($with)->select('*');
        $loadData = DxAdapter::load($data);
        return $loadData;
    }

    public function createCctv(array $cctvData): Cctv
    {
        DB::beginTransaction();
        try {
            $cctvData = Cctv::query()->create($cctvData);
            DB::commit();
            return $cctvData;
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString());
            throw new \RuntimeException("Failed to create cctv");
        }
    }

    public function updateCctv(String $cctvId, array $cctvData, ?string $url = null): Cctv
    {
        DB::beginTransaction();
        try {
            $cctv = Cctv::query()->where('id', $cctvId)->firstOrFail();
            $cctv->update($cctvData);
            DB::commit();
            if ($url) {
                Http::get($url);
            }
            return $cctv;
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Cctv not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to update cctv");
        }
    }

    public function deleteCctv(String $cctvId): void
    {
        try {
            $cctv = Cctv::query()->where('id', $cctvId)->firstOrFail();
            $cctv->delete();
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("cctv not found");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \RuntimeException("Failed to delete cctv");
        }
    }
}
