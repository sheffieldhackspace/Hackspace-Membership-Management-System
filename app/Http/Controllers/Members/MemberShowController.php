<?php

namespace App\Http\Controllers\Members;

use App\Data\MemberData;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Exceptions\UnauthorizedException;

class MemberShowController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Member $member)
    {
        /** @var User $user */
        $user = $request->user();


        if(
            !$user->checkPermissions([PermissionEnum::VIEWMEMBERS->value])
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
