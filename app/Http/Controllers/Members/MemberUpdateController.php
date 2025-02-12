<?php

namespace App\Http\Controllers\Members;

use App\Enums\MembershipType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Members\MembersUpdateRequest;
use App\Models\DiscordUser;
use App\Models\Member;

class MemberUpdateController extends Controller
{
    public function update(MembersUpdateRequest $request, Member $member)
    {
        $user = $request->user();
        $validated = $request->safe();

        $member->name = $validated['name'];
        $member->known_as = $validated['knownAs'];
        $member->save();

        if (empty($validated['postalAddress'])) {
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

        $emailAddressData = collect($validated['emailAddresses']);
        $member->emailAddresses()->whereNotIn('email_address', $emailAddressData->pluck('emailAddress'))->delete();

        $emailAddressData->each(function ($emailAddress) use ($member) {
            $member->emailAddresses()->updateOrCreate(
                [
                    'member_id' => $member->id,
                    'email_address' => $emailAddress['emailAddress'],
                ],
                [
                    'member_id' => $member->id,
                    'email_address' => $emailAddress['emailAddress'],
                    'is_primary' => $emailAddress['isPrimary'],
                ]
            );
        });

        $membershipType = MembershipType::from($request->input('membershipType'));
        if ($membershipType !== $member->getMembershipType()) {
            $member->setMembershipType($membershipType);
        }

        $trusteeValue = $request->input('trustee');
        if ($trusteeValue !== $member->getIsActiveTrustee()) {
            if ($trusteeValue) {
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

        if ($member->discordUser?->id !== $request->input('discordUserId') && $user->can('changeDiscordMember', $member)) {
            if ($request->input('discordUserId') === null) {
                $member->discordUser()->update(['member_id' => null]);
            } else {
                $discordUser = DiscordUser::find($request->input('discordUserId'));
                $member->discordUser()->save($discordUser);
            }
        }
    }
}
