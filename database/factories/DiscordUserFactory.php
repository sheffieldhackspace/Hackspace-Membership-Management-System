<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscordUser>
 */
class DiscordUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discord_id' => rand() << 32 | rand(),
            'username' => $this->faker->userName,
            'nickname' => $this->faker->optional(0.9)->name,
            'verified' => $this->faker->boolean,
            'avatar_hash' => $this->faker->sha256,
        ];
    }
}
