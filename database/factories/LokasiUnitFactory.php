<?php

namespace Database\Factories;

use App\Models\LokasiUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LokasiUnit>
 */
class LokasiUnitFactory extends Factory
{
    protected $model = LokasiUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lokasi' => $this->faker->unique()->city() . ' Lt. ' . $this->faker->numberBetween(1, 5),
            'kode_lokasi' => strtoupper($this->faker->lexify('LOK-???')),
            'keterangan'  => $this->faker->sentence(),
        ];
    }
}
