<?php

namespace App\Models;

use App\Enums\MembershipType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;

/**
 *
 *
 * @property string $id
 * @property string|null $user_id
 * @property string $name
 * @property string $known_as
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MembershipHistory|null $latestMembershipHistory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MembershipHistory> $membershipHistory
 * @property-read int|null $membership_history_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\MembershipHistory|null $oldestMembershipHistory
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\MemberFactory factory($count = null, $state = [])
 * @method static Builder<static>|Member hasActiveMembership()
 * @method static Builder<static>|Member membershipType(\App\Enums\MembershipType $membershipType)
 * @method static Builder<static>|Member newModelQuery()
 * @method static Builder<static>|Member newQuery()
 * @method static Builder<static>|Member query()
 * @method static Builder<static>|Member whereCreatedAt($value)
 * @method static Builder<static>|Member whereId($value)
 * @method static Builder<static>|Member whereKnownAs($value)
 * @method static Builder<static>|Member whereName($value)
 * @method static Builder<static>|Member whereUpdatedAt($value)
 * @method static Builder<static>|Member whereUserId($value)
 * @mixin \Eloquent
 */
class Member extends Model
{
    use HasFactory, Notifiable, HasUuids;
    protected $fillable = [
        'name',
        'known_as',
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

    public function user(): HasOne|User
    {
        return $this->hasOne(User::class);
    }

    public function getHasActiveMembership(): bool
    {
        /** @var MembershipHistory $latestHistoryEvent */
        $latestHistoryEvent = $this->latestMembershipHistory()->first();
        return $latestHistoryEvent->getIsActive();

    }

    public function setActiveMembership($value): void
    {
        if ($value === $this->getHasActiveMembership()) {
            return;
        }

        $currentMembershipType = $this->getMembershipType();

        if ($value) {
            $this->membershipHistory()->create([
                'membership_type' => $currentMembershipType === MembershipType::UnpaidKeyholder ? MembershipType::Keyholder : MembershipType::Member,
            ]);
        } else {
            $this->membershipHistory()->create([
                'membership_type' => $currentMembershipType === MembershipType::Keyholder ? MembershipType::UnpaidKeyholder : MembershipType::UnpaidMember,
            ]);
        }

        $this->save();
    }

    public function scopeHasActiveMembership(Builder|Member $query): Builder
    {
        return $query->whereHas('latestMembershipHistory',
            fn(Builder|MembershipHistory $query) => $query->isActive()
        );
    }


    public function getMembershipType(): MembershipType
    {
        /** @var MembershipHistory $membershipHistory */
        $membershipHistory = $this->latestMembershipHistory;
        return $membershipHistory->membership_type ?? MembershipType::UnpaidMember;

    }

    public function setMembershipType($value): void
    {
        $this->membershipHistory()->create([
            'membership_type' => $value,
        ]);
    }

    public function scopeMembershipType(Builder $query, MembershipType $membershipType): Builder
    {
        return $query->whereRelation('latestMembershipHistory', 'membership_type', '=', $membershipType->value);
    }

    public function getJoiningDate(): ?Carbon
    {
        if($this->oldestMembershipHistory) {
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


}
