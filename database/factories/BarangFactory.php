<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id'   => Category::factory(),
            'no_aset_local' => 'AST-' . date('Ym') . '-' . strtoupper($this->faker->bothify('?????')),
            'model'         => $this->faker->randomElement(['Lenovo Thinkpad T14', 'MacBook Pro M2', 'Dell Latitude 3420', 'Monitor LG 24"']),
            'type_spec'     => 'Core i7 16GB RAM 512GB SSD',
            'serial_number' => strtoupper($this->faker->unique()->bothify('SN-????-####')),
            'hostname'      => 'PC-' . strtoupper($this->faker->bothify('???##')),
            'buy_date'      => $this->faker->date(),
            'vendor'        => $this->faker->company(),
            'unit_loc'      => 'Head Office',
            'dept'          => 'IT',
            'pengguna'      => $this->faker->name(),
            'position_user' => 'Staff',
            'note'          => $this->faker->sentence(),
            'status'        => $this->faker->randomElement(['Tersedia', 'Dipinjam', 'Rusak']),
        ];
    }
}