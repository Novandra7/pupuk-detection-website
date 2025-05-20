<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shift\CreateShiftRequest;
use App\Http\Requests\Shift\UpdateShiftRequest;
use App\Interfaces\ShiftRepository;
use App\Models\Shift;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Pkt\StarterKit\Helpers\DxResponse;

class ShiftController extends Controller
{
    private ShiftRepository $shiftRepository;

    public function __construct(ShiftRepository $shiftRepository)
    {
        $this->shiftRepository = $shiftRepository;
    }

    public function shiftManagePage(Request $request)
    {
        return Inertia::render('MasterData/ShiftManage', [
            'shift' => Shift::all(),
            'leader_enabled' => config('granule-starter-kit.leader.LEADER_API_KEY') != null,
        ]);
    }

    public function dataProcessing(Request $request)
    {
        $loadData = $this->shiftRepository->getShiftDataProcessingWith([]);
        return DxResponse::json($loadData, $request);
    }

    public function create(CreateShiftRequest $request)
    {
        $validated = $request->validated();

        $validated['start_time'] = $validated['time'][0];
        $validated['end_time'] = $validated['time'][1];
        unset($validated['time']);

        try {
            $this->shiftRepository->createShift($validated);
            return redirect()->back()->with('message', 'Success to create shift');
        } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getTraceAsString());
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function update(String $id, UpdateShiftRequest $request)
    {
        $validated = $request->validated();

        $validated['start_time'] = $validated['time'][0];
        $validated['end_time'] = $validated['time'][1];
        unset($validated['time']);
        
        try {
            $this->shiftRepository->updateShift($id, $validated);
            return redirect()->back()->with('message', 'Success to update shift');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }

    public function switchStatus(String $id, Request $request)
    {
        try {
            $shiftData = ['is_active' => $request['is_active'] ? 1 : 0];
            $this->shiftRepository->updateShift($id, $shiftData);
            return response()->json(['message' => 'Success to switch shift status']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function delete(String $id, Request $request)
    {
        try {
            $this->shiftRepository->deleteShift($id);
            return redirect()->back()->with('message', 'Success to delete shift');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['message' => $th->getMessage()]);
        }
    }
}
