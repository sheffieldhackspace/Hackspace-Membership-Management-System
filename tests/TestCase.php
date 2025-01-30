<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function asAdminUser(): User
    {
        $user = User::factory()->isAdmin()->create();
        $this->actingAs($user);

        return $user;
    }

    /**
     * Asserts that the given value only contains unique values
     */
    public function assertContainsOnlyUniqueValues(iterable $iterable): void
    {
        $array = is_array($iterable) ? $iterable : iterator_to_array($iterable);
        assert(count($array) === count(array_unique($array)), 'Array contains duplicate values');
    }
}
