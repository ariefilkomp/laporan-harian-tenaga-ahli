<?php

namespace Database\Seeders;

use App\Models\LaporanHarian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LaporanHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LaporanHarian::factory(40)->create();
    }
}
