<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    protected $primaryKey = 'id';
    protected $connection = 'mysql1';
    protected $table = 'ms_cctv_sources';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'ms_warehouse_id');
    }
}
