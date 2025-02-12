<?php

namespace App\Models;

use App\Enums\PermissionEnum;
use App\Events\UserCreatedEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $id
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DiscordUser|null $discordUser
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Member> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, HasUuids, Notifiable {
        getAllPermissions as protected traitGetAllPermissions;
    }

    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $guard_name = 'web';

    protected $dispatchesEvents = [
        'created' => UserCreatedEvent::class,
    ];

    protected $with = [
        'discordUser',
        'members',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function discordUser(): HasOne
    {
        return $this->hasOne(DiscordUser::class);
    }

    /**
     * Check if the user or any of their members has any of the given permissions.
     * This should be the main way to check permissions for a user.
     *
     * @param  array<string|PermissionEnum>|Collection<string|PermissionEnum>|string|PermissionEnum  $permissions
     */
    public function checkPermissions(array|Collection|string|PermissionEnum $permissions): bool
    {
        if (is_string($permissions) || $permissions instanceof PermissionEnum) {
            $permissions = [$permissions];
        }

        return $this->hasAnyPermission($permissions) || $this->members->contains(fn (Member $member) => $member->hasAnyPermission($permissions));
    }

    /**
     * Get all permissions for the user and their members.
     *
     * @return Collection<string>
     */
    public function getAllPermissions(): Collection
    {
        $memberPermissions = $this->members->map(fn (Member $member) => $member->getAllPermissions())->flatten();

        return $this->traitGetAllPermissions()
            ->union($memberPermissions)
            ->unique('name');
    }

    /**
     * Add a where has clause for discord user with the passed id.
     *
     * @return Builder<User>
     *
     * @static
     */
    public static function whereDiscordId(string $discordId): Builder
    {
        return User::whereHas(
            'discordUser',
            fn (DiscordUser|Builder $discordUserQuery) => $discordUserQuery->where('discord_id', $discordId)
        );
    }
}
