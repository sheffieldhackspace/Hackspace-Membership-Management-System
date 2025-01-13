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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MembershipHistory> $membershipHistory
 * @property-read int|null $membership_history_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\MemberFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member hasActiveMembership()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereKnownAs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUserId($value)
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

    public function user(): HasOne|User
    {
        return $this->hasOne(User::class);
    }

    public function getHasActiveMembership(): bool
    {
        /** @var MembershipHistory $latestHistoryEvent */
        $latestHistoryEvent = $this->latestMembershipHistory()->first();
        return $latestHistoryEvent?->is_active ?? false;

    }

    public function setActiveMembership($value): void
    {
        if ($value === $this->has_active_membership) {
            return;
        }

        if ($value) {
            $this->membershipHistory()->create([
                'membership_type' => $this->membership_type === MembershipType::UnpaidKeyholder ? MembershipType::Keyholder : MembershipType::Member,
            ]);
        } else {
            $this->membershipHistory()->create([
                'membership_type' => $this->membership_type === MembershipType::Keyholder ? MembershipType::UnpaidKeyholder : MembershipType::UnpaidMember,
            ]);
        }

        $this->has_active_membership = $value;
        $this->save();
    }

    public function scopeHasActiveMembership(Builder|Member $query): Builder
    {
        return $query->latestMembershipHistory()->first()->isActive();
    }


    public function getMembershipType(): MembershipType
    {
        /** @var MembershipHistory $membershipHistory */
        $membershipHistory = $this->latestMembershipHistory()->first();
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
        $membershipHistory = $this->membershipHistory()->oldest()->first();

        if($membershipHistory) {
            return Carbon::make($membershipHistory->created_at);
        }
        return null;
    }

    public function setJoiningDate(Carbon $value): void
    {
        $this->membershipHistory()->oldest()->first()->update([
            'created_at' => $value->toDateTimeString(),
            'updated_at' => $value->toDateTimeString(),
        ]);
    }


}
