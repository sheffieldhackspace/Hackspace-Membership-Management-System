<?php

namespace App\Console\Commands;

use App\Jobs\PutDiscordMembersJob;
use Illuminate\Console\Command;

class PutDiscordMembersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:put-discord-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the database with all of the members of the discord guild';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        dispatch(new PutDiscordMembersJob);

        return 0;
    }
}
