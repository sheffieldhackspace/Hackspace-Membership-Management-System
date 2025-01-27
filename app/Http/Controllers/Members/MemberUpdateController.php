<?php

namespace App\Http\Controllers\Members;

use App\Enums\MembershipType;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Members\MembersUpdateRequest;
use App\Models\Member;
use App\Models\User;

class MemberUpdateController extends Controller
{

    public function update(MembersUpdateRequest $request, Member $member, User $user)
    {
        $validated = $request->validated();

        $member->name = $validated['name'];
        $member->known_as = $validated['knownAs'];
        $member->save();

        if(empty($validated['postalAddress'])){
            $member->postalAddress()->delete();
        } else {
            $member->postalAddress()->updateOrCreate(
                ['member_id' => $member->id],
                [
                    'line_1' => $validated['postalAddress']['line1'],
                    'line_2' => $validated['postalAddress']['line2'],
                    'line_3' => $validated['postalAddress']['line3'],
                    'city' => $validated['postalAddress']['city'],
                    'county' => $validated['postalAddress']['county'],
                    'postcode' => $validated['postalAddress']['postcode'],
                ]
            );
        }

        $emailAddressData = collect($request->get('emailAddresses'));
        $member->emailAddresses()->whereNotIn('id', $emailAddressData->pluck('id'))->delete();

        $emailAddressData->each(function($emailAddress) use ($member){
            $member->emailAddresses()->updateOrCreate(
                ['id' => $emailAddress['id']],
                [
                    'member_id' => $member->id,
                    'email_address' => $emailAddress['emailAddress'],
                    'is_primary' => $emailAddress['isPrimary'],
                ]
            );
        });

        $membershipType = $request->get('membershipType');
        if($membershipType !== null){
            $membershipType = MembershipType::from($membershipType);

            if ($membershipType != $member->getMembershipType()) {
                $member->setMembershipType($membershipType);
            }
        }

        $trusteeValue = $request->get('trustee');
        if($trusteeValue !== null && $trusteeValue !== $member->getIsActiveTrustee()){
            if($trusteeValue){
                $member->trusteeHistory()->create([
                    'member_id' => $member->id,
                    'elected_at' => now(),
                ]);
            } else {
                $member->latestTrusteeHistory()->update([
                    'resigned_at' => now(),
                ]);
            }
        }
    }
}
