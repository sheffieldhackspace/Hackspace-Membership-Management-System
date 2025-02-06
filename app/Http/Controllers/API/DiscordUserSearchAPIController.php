<?php

namespace App\Http\Controllers\API;

use App\Data\DiscordUserData;
use App\Http\Requests\DiscordUsers\DiscordUserSearchRequest;
use App\Models\DiscordUser;
use Illuminate\Database\Eloquent\Builder;

class DiscordUserSearchAPIController
{
    public function search(DiscordUserSearchRequest $request)
    {
        $validated = $request->safe()->collect();

        $discordUsers = DiscordUser::where(function (Builder|DiscordUser $discordUserQuery) use ($validated) {
            $discordUserQuery->whereLike('username', $validated->get('term').'%')
                ->orWhereLike('nickname', $validated->get('term').'%');
        })
            ->limit($validated->get('limit'))
            ->get();

        return response()->json(DiscordUserData::collect($discordUsers));
    }
}
