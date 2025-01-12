<?

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MembershipType;

/**
 * @property MembershipType $membership_type
 * @property-read Member $member
 * @property-read bool $is_active
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
