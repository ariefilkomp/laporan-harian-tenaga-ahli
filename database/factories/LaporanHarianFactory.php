<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LaporanHarian>
 */
class LaporanHarianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = new Carbon('first day of ' . date('F Y'));
        return [
            'user_id' => 1,
            'tanggal' => $date->addDays(rand(0, 30)),
            'uraian_pekerjaan' => fake()->text(),
        ];
    }
}
