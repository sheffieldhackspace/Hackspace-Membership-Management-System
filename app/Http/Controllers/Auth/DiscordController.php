<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\DiscordAuthenticationException;
use App\Http\Controllers\Controller;
use App\Services\Discord\DiscordProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class DiscordController extends Controller
{
    /**
     * Redirect the user to the Discord authentication page.
     *
     * @throws DiscordAuthenticationException
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
     *
     * @throws DiscordAuthenticationException
     */
    public function callback(): RedirectResponse
    {
        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord');

        try {
            $discordUser = $driver
                ->user();
        } catch (InvalidStateException $e) {
            throw DiscordAuthenticationException::errorRetrievingUserData($e);
        }

        if ($discordUser->isUserInGuild(config('services.discord.guild_id'))) {
            $discordUser->saveToSession();

            $user = $discordUser->getUserModel();
            if ($user->discordUser->verified === false && $discordUser->verified === true) {
                $user->discordUser->update(['verified' => true]);
            }

            // TODO add option to remember login
            Auth::login($user);

            return redirect()->intended(route('dashboard'));

        } else {
            throw DiscordAuthenticationException::notInDiscordGuild(config('services.discord.guild_id'));
        }
    }
}
