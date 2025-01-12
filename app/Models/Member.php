<?php

namespace App\Models;

use App\Enums\MembershipType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
 * @property bool $has_active_membership
 * @property Carbon $joining_date
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MembershipHistory> $membershipHistory
 * @property-read int|null $membership_history_count
 * @property MembershipType $membership_type
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\MemberFactory factory($count = null, $state = [])
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
        'membership_type',
        'has_active_membership',
    ];

    protected $attributes = [
        'has_active_membership' => false,
        'membership_type' => MembershipType::UnpaidMember,
    ];

    public function membershipHistory(): HasMany
    {
        return $this->hasMany(MembershipHistory::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * @return Attribute
     * Provides the has_active_membership attribute for the model
     * This attribute is a boolean that is true if the member has an active membership and false otherwise.
     *
     * The attribute can be written to to change the membership status of the member.
     * Keyholders will become unpaid keyholders, members will become unpaid members and vice versa.
     */
    protected function hasActiveMembership(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                /** @var MembershipHistory $latestHistoryEvent */
                $latestHistoryEvent = $this->membershipHistory()->latest()->first();
                return $latestHistoryEvent->is_active;
            },
            set: function ($value): void {
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
        );
    }

    /**
     * @return Attribute
     *
     * Provides the membership_type attribute for the model
     * This attribute is a MembershipType enum that represents the current membership status of the member.
     *
     * The attribute can be written to to change the membership status of the member.
     * The membership status will be recorded in the membership history.
     */
    public function membershipType(): Attribute
    {
        return Attribute::make(
            get: fn(): MembershipType => $this->membershipHistory()->latest()->first()->membership_type,
            set: fn($value) => $this->membershipHistory()->create([
                'membership_type_from' => $this->membership_type,
                'membership_type_to' => $value,
            ])
        );
    }

    /**
     * @return Attribute
     *
     * Provides the joining_date attribute for the model
     * This attribute is a Carbon Date that represents when the member joined the space.
     *
     * The attribute can be written to to change the date of the first membership history entry.
     */
    public function joiningDate(): Attribute
    {
        return Attribute::make(
            get: fn(): Carbon => Carbon::make($this->membershipHistory()->oldest()->first()->created_at),
            set: fn(Carbon $value) => $this->membershipHistory()->oldest()->first()->update([
                'created_at' => $value->toDateTimeString(),
                'updated_at' => $value->toDateTimeString(),
            ])
        );
    }


}
