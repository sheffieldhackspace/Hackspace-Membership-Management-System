<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\DiscordAuthenticationException;
use App\Http\Controllers\Controller;
use App\Services\Discord\DiscordProvider;
use App\Services\Discord\DiscordUser;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class DiscordController extends Controller
{
    /**
     * Redirect the user to the Discord authentication page.
     */
    public function redirect(): RedirectResponse
    {
        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord');

        return $driver
            ->redirect();
    }

    /**
     * Obtain the user information from Discord.
     */
    public function callback()
    {
        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord');

        /** @var DiscordUser $user */
        $user = $driver
            ->user();

        if ($user->isUserInGuild(config('services.discord.guild_id'))) {
            $user->saveUserInSession();
        } else {
            throw DiscordAuthenticationException::notInDiscordGuild(config('services.discord.guild_id'));
        }
    }
}
