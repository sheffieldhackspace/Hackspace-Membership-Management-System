<?php

namespace App\Services\Discord;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            $user = Http::acceptJson()
                ->withToken($token)
                ->get($userUrl)
                ->json();

            $user['guilds'] = Http::acceptJson()
                ->withToken($token)
                ->get($guildsUrl)
                ->json();

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
            'email' => $user['email'] ?? null,
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

    public function getAccessTokenResponse($code)
    {
        return Http::acceptJson()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->getTokenUrl(), $this->getTokenFields($code))
            ->json();
    }

    protected function getRefreshTokenResponse($refreshToken)
    {
        return Http::acceptJson()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->getTokenUrl(), [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ])->json();
    }
}
