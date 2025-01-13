<?php

namespace Database\Factories;

use App\Models\EmailAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailAddressFactory extends Factory
{
    protected $model = EmailAddress::class;

    public function definition(): array
    {
        return [
            'email_address' => $this->faker->unique()->safeEmail,
            'verified_at' => $this->faker->optional()->dateTime,
        ];
    }

    public function primary(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => true,
            ];
        });
    }
}
