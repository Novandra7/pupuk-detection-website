<?php

namespace App\Interfaces;

use App\Models\Cctv;
use Illuminate\Contracts\Database\Query\Builder;

interface CctvRepository
{
    public function getCctvDataProcessingWith(array $with = null): Builder;
    public function createCctv(array $cctvData): Cctv;
    public function updateCctv(String $cctvUuid, array $cctvData): Cctv;
    public function deleteCctv(String $cctvUuid): void;
}
