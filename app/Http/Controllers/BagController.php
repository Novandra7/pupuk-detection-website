<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bag\CreateBagRequest;
use App\Http\Requests\Bag\UpdateBagRequest;
use App\Interfaces\BagRepository;
use App\Models\Bag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Pkt\StarterKit\Helpers\DxResponse;

class BagController extends Controller
{
    private BagRepository $bagRepository;

    public function __construct(BagRepository $bagRepository)
    {
        $this->bagRepository = $bagRepository;
    }

    public function bagManagePage(Request $request)
    {
        return Inertia::render('MasterData/BagManage', [
            'roles' => Bag::all(),
            'leader_enabled' => config('granule-starter-kit.leader.LEADER_API_KEY') != null,
        ]);
    }

    public function dataProcessing(Request $request)
    {
        $loadData = $this->bagRepository->getBagDataProcessingWith([]);
        return DxResponse::json($loadData, $request);
    }

    public function create(CreateBagRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->bagRepository->createBag($validated);
            return redirect()->back()->with('message', 'Success to create bag');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function update(String $id, UpdateBagRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->bagRepository->updateBag($id, $validated);
            return redirect()->back()->with('message', 'Success to update bag');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function switchStatus(String $id, Request $request)
    {
        try {
            $bagData = ['is_active' => $request['is_active'] ? 1 : 0];
            $this->bagRepository->updateBag($id, $bagData);
            return response()->json(['message' => 'Success to switch bag status']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function delete(String $id, Request $request)
    {
        try {
            $this->bagRepository->deleteBag($id);
            return redirect()->back()->with('message', 'Success to delete bag');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
}
