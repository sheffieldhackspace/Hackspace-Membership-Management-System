<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MembershipHistory;
use App\Enums\MembershipType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipHistory>
 */
class MembershipHistoryFactory extends Factory
{
    protected $model = MembershipHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-3 year', 'now');
        return [
            'member_id' => Member::factory(),
            'membership_type' => $this->faker->randomElement(MembershipType::cases()),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
