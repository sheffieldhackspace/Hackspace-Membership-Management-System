<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class MemberFactory extends Factory
{
    use withFaker;
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName;
        return [
            'name' => "{$firstName} {$this->faker->lastName}",
            'known_as' => $firstName,

        ];
    }
}
