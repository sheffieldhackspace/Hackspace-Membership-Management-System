<?php

namespace App\Services\Discord;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use MartinBean\Laravel\Socialite\DiscordProvider as BaseDiscordProvider;

class DiscordProvider extends BaseDiscordProvider
{
    protected $scopes = [
        'email',
        'identify',
        'guilds',
    ];

    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);

        $this->redirectUrl(route('discord.callback', [], true));
    }

    protected function getUserByToken($token)
    {
        $userUrl = 'https://discord.com/api/users/@me';
        $guildsUrl = 'https://discord.com/api/users/@me/guilds';

        try {
            $user = json_decode($this->getHttpClient()->get($userUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ])->getBody(), true);

            $user['guilds'] = json_decode($this->getHttpClient()->get($guildsUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ])->getBody(), true);

            return $user;
        } catch (GuzzleException $e) {
            throw $e;
        }
    }

    protected function mapUserToObject(array $user)
    {
        return new DiscordUser()->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['username'],
            'nickname' => $user['global_name'],
            'email' => $user['email'],
            'avatar' => sprintf('https://cdn.discordapp.com/avatars/%s/%s.png', $user['id'], $user['avatar']),
            'guilds' => $user['guilds'],
            'verified' => $user['verified'],
        ]);
    }

    public function user(): DiscordUser
    {
        /** @var DiscordUser $user */
        $user = parent::user();

        return $user;
    }
}
