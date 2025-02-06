<?php

namespace App\Models;

use App\Enums\MembershipType;
use App\Events\MemberCreatedEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $id
 * @property string|null $user_id
 * @property string $name
 * @property string $known_as
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\DiscordUser|null $discordUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmailAddress> $emailAddresses
 * @property-read int|null $email_addresses_count
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\MembershipHistory|null $latestMembershipHistory
 * @property-read \App\Models\TrusteeHistory|null $latestTrusteeHistory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MembershipHistory> $membershipHistory
 * @property-read int|null $membership_history_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\MembershipHistory|null $oldestMembershipHistory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\PostalAddress|null $postalAddress
 * @property-read \App\Models\EmailAddress|null $primaryEmailAddress
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrusteeHistory> $trusteeHistory
 * @property-read int|null $trustee_history_count
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\MemberFactory factory($count = null, $state = [])
 * @method static Builder<static>|Member hasActiveMembership()
 * @method static Builder<static>|Member isTrustee()
 * @method static Builder<static>|Member membershipType(\App\Enums\MembershipType $membershipType)
 * @method static Builder<static>|Member newModelQuery()
 * @method static Builder<static>|Member newQuery()
 * @method static Builder<static>|Member onlyTrashed()
 * @method static Builder<static>|Member permission($permissions, $without = false)
 * @method static Builder<static>|Member query()
 * @method static Builder<static>|Member role($roles, $guard = null, $without = false)
 * @method static Builder<static>|Member whereCreatedAt($value)
 * @method static Builder<static>|Member whereDeletedAt($value)
 * @method static Builder<static>|Member whereId($value)
 * @method static Builder<static>|Member whereKnownAs($value)
 * @method static Builder<static>|Member whereName($value)
 * @method static Builder<static>|Member whereUpdatedAt($value)
 * @method static Builder<static>|Member whereUserId($value)
 * @method static Builder<static>|Member withTrashed()
 * @method static Builder<static>|Member withoutPermission($permissions)
 * @method static Builder<static>|Member withoutRole($roles, $guard = null)
 * @method static Builder<static>|Member withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Member extends Model
{
    use HasFactory, HasRoles, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'known_as',
    ];

    protected $guard_name = 'member';

    protected $dispatchesEvents = [
        'created' => MemberCreatedEvent::class,
    ];

    public function membershipHistory(): HasMany
    {
        return $this->hasMany(MembershipHistory::class)->latest();
    }

    public function latestMembershipHistory(): HasOne
    {
        return $this->hasOne(MembershipHistory::class)->latestOfMany();
    }

    public function oldestMembershipHistory(): HasOne
    {
        return $this->hasOne(MembershipHistory::class)->oldestOfMany();
    }

    public function trusteeHistory(): HasMany
    {
        return $this->hasMany(TrusteeHistory::class)->latest();
    }

    public function latestTrusteeHistory(): HasOne
    {
        return $this->hasOne(TrusteeHistory::class)->latestOfMany();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function emailAddresses(): HasMany
    {
        return $this->hasMany(EmailAddress::class);
    }

    public function primaryEmailAddress(): HasOne
    {
        return $this->hasOne(EmailAddress::class)->isPrimary();
    }

    public function postalAddress(): HasOne
    {
        return $this->hasOne(PostalAddress::class);
    }

    public function discordUser(): HasOne
    {
        return $this->hasOne(DiscordUser::class);
    }

    public function getHasActiveMembership(): bool
    {
        $latestHistoryEvent = $this->latestMembershipHistory;

        return $latestHistoryEvent?->getIsActive() ?? false;

    }

    public function setActiveMembership($value): void
    {
        if ($value === $this->getHasActiveMembership()) {
            return;
        }

        $currentMembershipType = $this->getMembershipType();

        if ($value) {
            $this->membershipHistory()->create([
                'membership_type' => $currentMembershipType === MembershipType::UNPAIDKEYHOLDER ? MembershipType::KEYHOLDER : MembershipType::MEMBER,
            ]);
        } else {
            $this->membershipHistory()->create([
                'membership_type' => $currentMembershipType === MembershipType::KEYHOLDER ? MembershipType::UNPAIDKEYHOLDER : MembershipType::UNPAIDMEMBER,
            ]);
        }

        $this->save();
    }

    public function scopeHasActiveMembership(Builder|Member $query): Builder
    {
        return $query->whereHas('latestMembershipHistory',
            fn (Builder|MembershipHistory $query) => $query->isActive()
        );
    }

    public function getMembershipType(): MembershipType
    {
        /** @var MembershipHistory $membershipHistory */
        $membershipHistory = $this->latestMembershipHistory;

        return $membershipHistory->membership_type ?? MembershipType::UNPAIDMEMBER;

    }

    public function setMembershipType(MembershipType $membershipType): void
    {

        $this->membershipHistory()->create([
            'membership_type' => $membershipType->value,
        ]);
    }

    public function scopeMembershipType(Builder $query, MembershipType $membershipType): Builder
    {
        return $query->whereRelation('latestMembershipHistory', 'membership_type', '=', $membershipType->value);
    }

    public function getJoiningDate(): ?Carbon
    {
        if ($this->oldestMembershipHistory) {
            return Carbon::make($this->oldestMembershipHistory->created_at);
        }

        return null;
    }

    public function setJoiningDate(Carbon $value): void
    {
        $this->oldestMembershipHistory->update([
            'created_at' => $value->toDateTimeString(),
            'updated_at' => $value->toDateTimeString(),
        ]);
    }

    public function getIsActiveTrustee(): bool
    {
        $latestTrusteeHistory = $this->latestTrusteeHistory;

        if (! $latestTrusteeHistory) {
            return false;
        }

        if (! $latestTrusteeHistory->resigned_at) {
            return true;
        }

        return false;
    }

    public function scopeIsTrustee(Builder $query): Builder
    {
        return $query->whereHas('latestTrusteeHistory', fn (Builder|TrusteeHistory $query) => $query->isTrustee());
    }
}
