<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function asAdminUser(): void
    {
        $this->actingAs(User::factory()->create());
    }
}
