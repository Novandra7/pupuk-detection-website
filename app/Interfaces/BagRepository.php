<?php

namespace App\Interfaces;

use App\Models\Bag;
use Illuminate\Contracts\Database\Query\Builder;

interface BagRepository
{
    public function getBagDataProcessingWith(array $with = null): Builder;
    public function createBag(array $BagData): Bag;
    public function updateBag(String $BagId, array $BagData): Bag;
    public function deleteBag(String $BagId): void;
}
