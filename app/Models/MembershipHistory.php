<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\MembershipType;

/**
 * 
 *
 * @property string $id
 * @property string $member_id
 * @property MembershipType $membership_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $is_active
 * @property-read \App\Models\Member $member
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory whereMembershipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MembershipHistory extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'membership_type',
    ];

    protected $casts = [
        'membership_type' => MembershipType::class,
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get:function () {
                return $this->membership_type === MembershipType::Keyholder || $this->membership_type === MembershipType::Member;
            }
        );
    }

}
