<?php

namespace App\Models;

use Database\Factories\DiscordUserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $discord_id
 * @property string|null $user_id
 * @property string|null $member_id
 * @property string $username
 * @property string $nickname
 * @property bool $verified
 * @property string|null $avatar_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member|null $member
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\DiscordUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereAvatarHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereDiscordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordUser whereVerified($value)
 *
 * @mixin \Eloquent
 */
class DiscordUser extends Model
{
    /** @use HasFactory<DiscordUserFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'discord_id',
        'username',
        'nickname',
        'verified',
        'avatar_hash',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'discord_id' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getAvatar(): string
    {
        return sprintf('https://cdn.discordapp.com/avatars/%s/%s.png', $this->discord_id, $this->avatar_hash);
    }
}
