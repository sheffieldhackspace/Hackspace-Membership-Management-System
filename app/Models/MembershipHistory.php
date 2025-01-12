<?

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MembershipType;

/**
 * @property MembershipType $membership_type_from
 * @property MembershipType $membership_type_to
 */
class MembershipHistory extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'membership_type_from',
        'membership_type_to',
    ];

    protected $casts = [
        'membership_type_from' => MembershipType::class,
        'membership_type_to' => MembershipType::class,
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function is_active(): bool
    {
        return $this->membership_type_to === MembershipType::Keyholder || $this->membership_type_to === MembershipType::Member;
    }

}
