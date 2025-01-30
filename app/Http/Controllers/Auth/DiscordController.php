<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Services\Discord\DiscordUser;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use MartinBean\Laravel\Socialite\DiscordProvider;

class DiscordController extends Controller
{
    /**
     * Redirect the user to the Discord authentication page.
     */
    public function redirect(): RedirectResponse
    {
        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord-with-guilds');

        return $driver
            ->redirect();
    }

    /**
     * Obtain the user information from Discord.
     */
    public function callback()
    {
        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord-with-guilds');

        /** @var DiscordUser $user */
        $user = $driver
            ->user();

        if ($user->isUserInGuild(config('services.discord.guild_id'))) {
            // Authentication passed...
        } else {
            throw UnauthorizedException::notInDiscordGuild();
        }
    }
}
