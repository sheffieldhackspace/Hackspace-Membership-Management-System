<?php

namespace App\Models;

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
 * @property string $line_1
 * @property string|null $line_2
 * @property string|null $line_3
 * @property string|null $city
 * @property string|null $county
 * @property string $postcode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member $member
 * @method static \Database\Factories\PostalAddressFactory factory($count = null, $state = [])
 * @method static Builder<static>|PostalAddress newModelQuery()
 * @method static Builder<static>|PostalAddress newQuery()
 * @method static Builder<static>|PostalAddress query()
 * @method static Builder<static>|PostalAddress whereCity($value)
 * @method static Builder<static>|PostalAddress whereCounty($value)
 * @method static Builder<static>|PostalAddress whereCreatedAt($value)
 * @method static Builder<static>|PostalAddress whereId($value)
 * @method static Builder<static>|PostalAddress whereLine1($value)
 * @method static Builder<static>|PostalAddress whereLine2($value)
 * @method static Builder<static>|PostalAddress whereLine3($value)
 * @method static Builder<static>|PostalAddress whereMemberId($value)
 * @method static Builder<static>|PostalAddress wherePostcode($value)
 * @method static Builder<static>|PostalAddress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PostalAddress extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'line_1',
        'line_2',
        'line_3',
        'city',
        'county',
        'postcode',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
