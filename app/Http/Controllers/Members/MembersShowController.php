<?php

namespace App\Http\Controllers\Members;

use App\Data\MemberData;
use App\Data\MembershipTypeData;
use App\Enums\MembershipType;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Members\MembersFilterRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\Permission\Exceptions\UnauthorizedException;

class MembersShowController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Member $member)
    {
        /** @var User $user */
        $user = $request->user();


        if(
            !$user->membersHavePermission(PermissionEnum::VIEWMEMBERS->value)
            && !$user->members->contains('id','=',$member->id)
        ){
            throw UnauthorizedException::forPermissions([PermissionEnum::VIEWOWNMEMBER->value]);
        }

        $member->load([
            'emailAddresses',
            'postalAddress',
            'membershipHistory',
            'trusteeHistory',
        ]);

        return Inertia::render('Members/Show', [
            'member' => MemberData::fromModel($member),
        ]);
    }

}
