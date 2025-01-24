<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Http\Requests\Members\MembersUpdateRequest;
use App\Models\Member;

class MemberUpdateController extends Controller
{

    /**
     * Updates a existing resource in storage.
     */
    public function update(MembersUpdateRequest $request, Member $member)
    {
        $data = $request->validated();
        dd($data);
    }


}
