<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cctv\CreateCctvRequest;
use App\Http\Requests\Cctv\UpdateCctvRequest;
use App\Interfaces\CctvRepository;
use App\Models\Cctv;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Pkt\StarterKit\Helpers\DxResponse;
use Spatie\Permission\Models\Role;

class CctvController extends Controller
{
    private CctvRepository $cctvRepository;

    public function __construct(CctvRepository $cctvRepository)
    {
        $this->cctvRepository = $cctvRepository;
    }

    public function cctvManagePage(Request $request)
    {
        return Inertia::render('MasterData/CctvManage', [
            'warehouse' => Warehouse::where('is_active', true)->get(),
            'leader_enabled' => config('granule-starter-kit.leader.LEADER_API_KEY') != null,
        ]);
    }

    public function dataProcessing(Request $request)
    {
        $loadData = $this->cctvRepository->getCctvDataProcessingWith(["warehouse"]);
        return DxResponse::json($loadData, $request);
    }

    public function create(CreateCctvRequest $request)
    {
        $validated = $request->validated();

        $count = Cctv::count();
        $endpoint = 'Kamera' . ($count + 1);
        $validated['endpoint'] = $endpoint;

        try {
            $this->cctvRepository->createCctv($validated);
            return redirect()->back()->with('message', 'Success to create cctv');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function update(String $id, UpdateCctvRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->cctvRepository->updateCctv($id, $validated);
            return redirect()->back()->with('message', 'Success to update cctv');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function switchStatus(String $id, Request $request)
    {
        try {
            $is_active = $request['is_active'] ? 1 : 0;
            $cctvData = ['is_active' => $is_active];

            $channel = $request['channel'];
            $baseUrl = env('VITE_API_BASE_URL');
            $action = $is_active ? 'start_predict' : 'stop_predict';
            $url = "{$baseUrl}/{$action}/{$channel}";

            Http::get($url);

            $this->cctvRepository->updateCctv($id, $cctvData);
            return response()->json(['message' => 'Success to switch cctv status']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function delete(String $id, Request $request)
    {
        try {
            $this->cctvRepository->deleteCctv($id);
            return redirect()->back()->with('message', 'Success to delete cctv');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
}
