<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PutDiscordMembersCommand;
use App\Jobs\PutDiscordMembersJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(PutDiscordMembersCommand::class)]
class PutDiscordMembersCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_get_discord_members_job()
    {
        // Fake the job dispatching
        Bus::fake();

        // Run the command
        $this->artisan(PutDiscordMembersCommand::class)->assertExitCode(0);

        // Assert the job was dispatched
        Bus::assertDispatched(PutDiscordMembersJob::class);
    }
}
