<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\CreateWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Interfaces\WarehouseRepository;
use Illuminate\Http\Request;
use App\Interfaces\WarehouserRepository;
use Inertia\Inertia;
use Pkt\StarterKit\Helpers\DxResponse;
use Spatie\Permission\Models\Role;

class WarehouseController extends Controller
{
    private WarehouseRepository $warehouserRepository;

    public function __construct(WarehouseRepository $warehouserRepository)
    {
        $this->warehouserRepository = $warehouserRepository;
    }

    public function warehouseManagePage(Request $request)
    {
        return Inertia::render('MasterData/WarehouseManage', [
            'roles' => Role::all(),
            'leader_enabled' => config('granule-starter-kit.leader.LEADER_API_KEY') != null,
        ]);
    }

    public function dataProcessing(Request $request)
    {
        $loadData = $this->warehouserRepository->getWarehouseDataProcessingWith([]);
        return DxResponse::json($loadData, $request);
    }

    public function create(CreateWarehouseRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->warehouserRepository->createWarehouse($validated);
            return redirect()->back()->with('message', 'Success to create warehouse');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function update(String $id, UpdateWarehouseRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->warehouserRepository->updateWarehouse($id, $validated);
            return redirect()->back()->with('message', 'Success to update warehouse');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function switchStatus(String $id, Request $request)
    {
        try {
            $warehouseData = ['is_active' => $request['is_active'] ? 1 : 0];
            $this->warehouserRepository->updateWarehouse($id, $warehouseData);
            return response()->json(['message' => 'Success to switch warehouse status']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function delete(String $id, Request $request)
    {
        try {
            $this->warehouserRepository->deleteWarehouse($id);
            return redirect()->back()->with('message', 'Success to delete warehouse');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
}
