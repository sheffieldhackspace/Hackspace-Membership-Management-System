<?php

namespace Database\Factories;

use App\Enums\MembershipType;
use App\Enums\RolesEnum;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\TrusteeHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
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
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function isAdmin(): static
    {
        return $this->has(Member::factory([
            'name' => 'Admin User',
            'known_as' => 'Admin',
        ])
            ->has(MembershipHistory::factory([
                'membership_type' => MembershipType::KEYHOLDER,
            ]))
            ->isTrustee()
        );
    }

    public function isPWUser(): static
    {
        return $this->afterCreating(fn (User $user) =>
            $user->assignRole(RolesEnum::PWUSER)
        );
    }
}
