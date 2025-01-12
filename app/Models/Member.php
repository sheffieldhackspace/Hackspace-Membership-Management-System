<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use MembershipType;

/**
 * @property string $name
 * @property string $known_as
 * @property MembershipType $membership_type
 * @property bool $has_active_membership
 * @property Carbon $joining_date
 * @property-read MembershipHistory $membershipHistory
 * @property-read User $user
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
            get: function () {
                $latestHistoryEvent = $this->membershipHistory()->latest()->first()->is_active;
            },
            set: function ($value) {
                if ($value === $this->has_active_membership) {
                    return;
                }

                if ($value) {
                    $this->membershipHistory()->create([
                        'membership_type_from' => $this->membership_type,
                        'membership_type_to' => $this->membership_type === MembershipType::UnpaidKeyholder ? MembershipType::Keyholder : MembershipType::Member,
                    ]);
                } else {
                    $this->membershipHistory()->create([
                        'membership_type_from' => $this->membership_type,
                        'membership_type_to' => $this->membership_type === MembershipType::Keyholder ? MembershipType::UnpaidKeyholder : MembershipType::UnpaidMember,
                    ]);
                }

                $this->has_active_membership = $value;
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
            get: fn() => $this->membershipHistory()->latest()->first()->membership_type_to,
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
            get: fn() => Carbon::make($this->membershipHistory()->oldest()->first()->created_at),
            set: fn(Carbon $value) => $this->membershipHistory()->oldest()->first()->update([
                'created_at' => $value->toDateTimeString(),
                'updated_at' => $value->toDateTimeString(),
            ])
        );
    }


}
