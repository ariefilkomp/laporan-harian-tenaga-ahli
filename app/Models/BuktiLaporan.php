<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiLaporan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function laporanHarian()
    {
        return $this->belongsTo(LaporanHarian::class);
    }
}
