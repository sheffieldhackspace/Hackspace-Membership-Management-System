<?php

namespace Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;

trait WithFakeDiscordApi
{
    use WithFaker;

    public function withFakeDiscordApi(): void
    {
        Http::preventStrayRequests();
    }

    /**
     * Fake the Discord API to return a collection of guild members.
     *
     * @param  int|array  $input  The number of guild members to generate or an array of guild members to return.
     * @param  int  $statusCode  The status code to return if it is more than 400 a null response will be returned with the passed status code.
     */
    public function fakeGuildMembersAPI(int|array $input, int $statusCode = 200): Factory
    {
        if ($statusCode >= 400) {
            return Http::fake([
                config('services.discord.api_url').'/guilds/*/members?*' => Http::response(null, $statusCode),
            ]);
        }
        if (is_int($input)) {
            if ($input < 100) {
                $sequence = Http::sequence();
                $responses = floor($input / 100);
                $remainder = $input % 100;
                for ($i = 0; $i < $responses; $i++) {
                    $sequence->push($this->generateFakeGuildMemberData(100), $statusCode);
                }
                if ($remainder > 0) {
                    $sequence->push($this->generateFakeGuildMemberData($remainder), $statusCode);
                }

                return Http::fake([
                    config('services.discord.api_url').'/guilds/*/members?*' => $sequence,
                ]);
            } else {
                return Http::fake([
                    config('services.discord.api_url').'/guilds/*/members?*' => Http::response($this->generateFakeGuildMemberData($input), $statusCode),
                ]);
            }
        } else {
            return Http::fake([
                config('services.discord.api_url').'/guilds/*/members?*' => Http::response($input, $statusCode),
            ]);
        }

    }

    protected function generateFakeGuildMemberData($count): array
    {
        $collection = [];

        for ($i = 0; $i < $count; $i++) {
            $guildMember = [
                'user' => [
                    'id' => rand() << 32 | rand(),
                    'username' => $this->faker->userName,
                    'global_name' => $this->faker->optional()->name,
                    'avatar' => $this->faker->sha256,
                ],
                'nick' => $this->faker->optional(0.8)->name,
                'avatar' => $this->faker->optional(0.3)->sha256,
            ];

            if (rand(1, 10) === 1) {
                $guildMember['user']['bot'] = true;
            }

            $collection[] = $guildMember;
        }

        return $collection;
    }
}
