<?php

namespace App\Http\Controllers\Members;

use App\Data\MemberData;
use App\Enums\MembershipType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Members\MembersFilterRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelData\PaginatedDataCollection;

class MembersUpdateController extends Controller
{

    /**
     * Updates a existing resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        //
    }


}
