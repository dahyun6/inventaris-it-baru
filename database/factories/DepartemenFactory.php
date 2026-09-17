<?php

namespace Database\Factories;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Departemen>
 */
class DepartemenFactory extends Factory
{
    protected $model = Departemen::class;

    public function definition(): array
    {
        return [
            'nama_departemen' => fake()->unique()->word() . ' ' . fake()->unique()->randomNumber(3),
        ];
    }
}
