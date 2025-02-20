<?php

namespace App\Jobs;

use App\DiscordData\GuildMemberData;
use App\Services\Discord\DiscordService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PutDiscordMembersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(DiscordService $discordService): void
    {
        $discordService->getMembersOfGuild()
            ->filter(fn (GuildMemberData $member) => $member->bot === false)
            ->each(function (GuildMemberData $member) use ($discordService) {
                $discordService->updateOrCreateUserFromGuildMember($member);
            });
    }
}
