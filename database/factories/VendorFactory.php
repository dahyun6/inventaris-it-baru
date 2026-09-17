<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'nama_vendor' => $this->faker->unique()->company(),
            'alamat'      => $this->faker->address(),
            'telepon'     => $this->faker->phoneNumber(),
            'email'       => $this->faker->safeEmail(),
        ];
    }
}
