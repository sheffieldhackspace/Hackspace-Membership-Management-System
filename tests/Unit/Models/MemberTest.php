<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Models\User::class)]
class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test(): void
    {
        $this->markTestIncomplete('Tests for the Member model have not been implemented yet.');


    }
}
