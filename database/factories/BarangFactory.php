<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    public function definition(): array
    {
        // Ambil kategori acak yang ada di database
        $category = Category::inRandomOrder()->first();

        $merk = match($category->nama_kategori ?? 'Laptop') {
            'Laptop' => $this->faker->randomElement(['Lenovo Thinkpad T14', 'MacBook Pro M2', 'Dell Latitude 3420']),
            'Monitor' => $this->faker->randomElement(['Dell 24 Inch', 'LG 27 Inch 4K', 'Samsung Curved']),
            'Printer' => $this->faker->randomElement(['Epson L3110', 'Brother DCP']),
            'Jaringan' => $this->faker->randomElement(['Mikrotik Router', 'Cisco Switch']),
            default => $this->faker->randomElement(['Logitech Mouse', 'Keyboard Mechanical']),
        };

        return [
            'category_id' => $category->id ?? 1,
            'nama_barang' => $merk,
            'serial_number' => strtoupper($this->faker->bothify('SN-????-####')),
            'status' => $this->faker->randomElement(['Tersedia', 'Dipinjam', 'Rusak']),
        ];
    }
}