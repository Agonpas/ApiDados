<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory(),
            'dice1' => $this->faker->numberBetween(1,6),
            'dice2' => $this->faker->numberBetween(1,6),
            'won' => function (array $attributes) {
                $sumaDices = $attributes['dice1'] + $attributes['dice2'];
                return $sumaDices === 7;
            },
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
