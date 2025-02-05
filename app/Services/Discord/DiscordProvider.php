<?php

namespace App\Services\Discord;

use App\Exceptions\DiscordAuthenticationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

/**
 * Discord OAuth2 Provider.
 * Based on martinbean/socialite-discord-provider but modified so much it made little sense to require and then extend the original.
 *
 * @method SocialiteDiscordUser user()
 *
 * @see https://github.com/martinbean/socialite-discord-provider Origial package this was based on
 * @see https://discord.com/developers/docs/topics/oauth2#authorization-url Discord OAuth2 documentation
 */
class DiscordProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = [
        'email',
        'identify',
        'guilds',
        'guilds.members.read',
    ];

    protected $scopeSeparator = ' ';

    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);

        $this->redirectUrl(route('discord.callback', [], true));
    }

    public function bot(): BotRedirectBuilder
    {
        return new BotRedirectBuilder($this->clientId);
    }

    /**
     * Get the access token response for the given code.
     *
     * @param  string  $code
     *
     * @throws DiscordAuthenticationException
     */
    public function getAccessTokenResponse($code): array
    {
        try {
            return Http::acceptJson()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->getTokenUrl(), $this->getTokenFields($code))
                ->throw()
                ->json();

        } catch (ConnectionException|RequestException $e) {
            throw DiscordAuthenticationException::errorRetrievingAccessToken($e);
        }
    }

    protected function getTokenUrl(): string
    {
        return 'https://discord.com/api/oauth2/token';
    }

    protected function getTokenFields($code): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://discord.com/api/oauth2/authorize', $state);
    }

    /**
     * Get the access token response for the given refresh token.
     *
     * @param  string  $refreshToken
     *
     * @throws DiscordAuthenticationException
     */
    protected function getRefreshTokenResponse($refreshToken): array
    {
        try {
            return Http::acceptJson()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->getTokenUrl(), [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ])
                ->throw()
                ->json();
        } catch (ConnectionException|RequestException $e) {
            throw DiscordAuthenticationException::errorRetrievingRefreshToken($e);
        }
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     *
     * @throws DiscordAuthenticationException
     */
    protected function getUserByToken($token): array
    {
        try {
            $userUrl = 'https://discord.com/api/users/@me';
            $guildsUrl = 'https://discord.com/api/users/@me/guilds';

            $user = Http::acceptJson()
                ->withToken($token)
                ->get($userUrl)
                ->throw()
                ->json();

            $user['guilds'] = Http::acceptJson()
                ->withToken($token)
                ->get($guildsUrl)
                ->throw()
                ->json();

            return $user;
        } catch (ConnectionException|RequestException $e) {
            throw DiscordAuthenticationException::errorRetrievingUserData($e);
        }
    }

    /**
     * Maps the user array to a DiscordUser object.
     */
    protected function mapUserToObject(array $user): SocialiteDiscordUser
    {
        return (new SocialiteDiscordUser)
            ->setRaw($user)
            ->map([
                'id' => $user['id'],
                'name' => $user['username'],
                'nickname' => $user['global_name'] ?? $user['username'],
                'email' => $user['email'] ?? null,
                'avatar' => $user['avatar'] ? sprintf('https://cdn.discordapp.com/avatars/%s/%s.png', $user['id'], $user['avatar']) : '',
                'avatar_hash' => $user['avatar'] ?? null,
                'guilds' => $user['guilds'],
                'verified' => $user['verified'],
            ]);
    }
}
