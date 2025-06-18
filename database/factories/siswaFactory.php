<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class siswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nisn' => fake()->unique()->randomNumber(9),
            'nama' => fake()->name(),
            'jns_kelamin' => fake()->randomElement(['L', 'P']),
            'kelas' => fake()->randomElement(['X', 'XI', 'XII']),
            'jurusan' => fake()->randomElement(['Teknik Kendaraan Ringan', 'Teknik Mesin Industri', 'Manajemen Perkantoran']),
        ];
    }
}
