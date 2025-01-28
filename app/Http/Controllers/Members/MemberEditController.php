<?php

namespace App\Http\Controllers\Members;

use App\Data\MemberData;
use App\Data\MembershipTypeData;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Exceptions\UnauthorizedException;

class MemberEditController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Member $member)
    {
        /** @var User $user */
        $user = $request->user();

        if(
            !$user->checkPermissions([PermissionEnum::EDITOWNMEMBER->value])
            && !$user->members->contains('id','=',$member->id)
        ){
            throw UnauthorizedException::forPermissions([PermissionEnum::EDITOWNMEMBER->value]);
        }

        $member->load([
            'emailAddresses',
            'postalAddress',
            'membershipHistory',
            'trusteeHistory',
        ]);

        return Inertia::render('Members/Edit', [
            'member' => MemberData::fromModel($member),
            'membershipTypes' => MembershipTypeData::getAll(),
            'canChangeMembershipType' => $user->can('changeMembershipType', $member),
        ]);
    }
}
