<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Discord\DiscordProvider;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class DiscordAdminController extends Controller
{
    /**
     * Redirect the user to the Discord authentication page.
     */
    public function connect(): RedirectResponse
    {
        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord');

        return $driver
            ->bot()
            ->permissions(8)
            ->guild(config('services.discord.guild_id'))
            ->disableGuildSelect()
            ->redirect();
    }
}
