<?php

namespace App\Interfaces;

use App\Models\Shift;
use Illuminate\Contracts\Database\Query\Builder;

interface ShiftRepository
{
    public function getShiftDataProcessingWith(array $with = null): Builder;
    public function createShift(array $shiftData): Shift;
    public function updateShift(String $shiftId, array $shiftData): Shift;
    public function deleteShift(String $shiftId): void;
}
