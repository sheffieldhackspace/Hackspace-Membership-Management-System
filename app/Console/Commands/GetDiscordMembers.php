<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetDiscordMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-discord-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the database with all of the members of the discord guild';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
