<?php

namespace Database\Factories;


use App\Models\Member;
use App\Models\TrusteeHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
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

    public function isTrustee(): self
    {
        return $this->afterCreating(fn (Member $member) =>
            TrusteeHistory::factory([
                'elected_at' => $this->faker->dateTimeBetween('-3 year', 'now'),
                'member_id' => $member->id
            ])->create()
        );
    }

    public function isExTrustee(): self
    {
        return $this->afterCreating(fn (Member $member) =>
            TrusteeHistory::factory([
                'elected_at' => $this->faker->dateTimeBetween('-3 year', '-1 year'),
                'resigned_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
                'member_id' => $member->id
            ])->create()
        );
    }
}
