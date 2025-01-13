<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\MembershipType;
use Illuminate\Database\Eloquent\Builder;

/**
 * 
 *
 * @property string $id
 * @property string $member_id
 * @property MembershipType $membership_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member $member
 * @method static \Database\Factories\MembershipHistoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|MembershipHistory isActive()
 * @method static Builder<static>|MembershipHistory newModelQuery()
 * @method static Builder<static>|MembershipHistory newQuery()
 * @method static Builder<static>|MembershipHistory query()
 * @method static Builder<static>|MembershipHistory whereCreatedAt($value)
 * @method static Builder<static>|MembershipHistory whereId($value)
 * @method static Builder<static>|MembershipHistory whereMemberId($value)
 * @method static Builder<static>|MembershipHistory whereMembershipType($value)
 * @method static Builder<static>|MembershipHistory whereUpdatedAt($value)
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

    public function getIsActive(): bool
    {
            return $this->membership_type === MembershipType::Keyholder || $this->membership_type === MembershipType::Member;
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where(fn (Builder|MembershipHistory $query) => $query
            ->where('membership_type', MembershipType::Keyholder)
            ->orWhere('membership_type', MembershipType::Member)
        );
    }
}
