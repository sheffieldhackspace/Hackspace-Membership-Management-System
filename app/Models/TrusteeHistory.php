<?php

namespace App\Models;

use Carbon\Carbon;
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
 * @property string|null $elected_at
 * @property string|null $resigned_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member $member
 * @method static \Database\Factories\TrusteeHistoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|TrusteeHistory isTrustee()
 * @method static Builder<static>|TrusteeHistory newModelQuery()
 * @method static Builder<static>|TrusteeHistory newQuery()
 * @method static Builder<static>|TrusteeHistory query()
 * @method static Builder<static>|TrusteeHistory whereCreatedAt($value)
 * @method static Builder<static>|TrusteeHistory whereElectedAt($value)
 * @method static Builder<static>|TrusteeHistory whereId($value)
 * @method static Builder<static>|TrusteeHistory whereMemberId($value)
 * @method static Builder<static>|TrusteeHistory whereResignedAt($value)
 * @method static Builder<static>|TrusteeHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TrusteeHistory extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'elected_at',
        'resigned_at',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeIsTrustee(Builder $query): Builder
    {
        return $query->where(fn (Builder|TrusteeHistory $query) => $query
            ->whereNotNull('elected_at' )
            ->whereNull('resigned_at')
        );
    }
}
