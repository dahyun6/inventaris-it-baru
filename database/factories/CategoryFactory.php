<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->unique()->word() . ' ' . $this->faker->unique()->randomNumber(3),
            'kode_prefix'   => strtoupper($this->faker->lexify('???')),
        ];
    }
}
