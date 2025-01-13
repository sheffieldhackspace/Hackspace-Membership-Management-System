<?php

namespace App\Models;

use App\Exceptions\MustHavePrimaryEmailException;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property string $email_address
 * @property bool $is_primary
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member $member
 * @method static \Database\Factories\EmailAddressFactory factory($count = null, $state = [])
 * @method static Builder<static>|EmailAddress isPrimary()
 * @method static Builder<static>|EmailAddress newModelQuery()
 * @method static Builder<static>|EmailAddress newQuery()
 * @method static Builder<static>|EmailAddress query()
 * @method static Builder<static>|EmailAddress whereCreatedAt($value)
 * @method static Builder<static>|EmailAddress whereEmailAddress($value)
 * @method static Builder<static>|EmailAddress whereId($value)
 * @method static Builder<static>|EmailAddress whereIsPrimary($value)
 * @method static Builder<static>|EmailAddress whereMemberId($value)
 * @method static Builder<static>|EmailAddress whereUpdatedAt($value)
 * @method static Builder<static>|EmailAddress whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class EmailAddress extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'email_address',
        'is_primary',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];


    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }


    public function scopeIsPrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }
}
