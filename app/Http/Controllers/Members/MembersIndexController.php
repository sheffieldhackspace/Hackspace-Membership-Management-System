<?php

namespace App\Http\Controllers\Members;

use App\Data\MemberData;
use App\Data\MembershipTypeData;
use App\Enums\MembershipType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Members\MembersFilterRequest;
use App\Models\Member;
use Inertia\Inertia;
use Spatie\LaravelData\PaginatedDataCollection;

class MembersIndexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MembersFilterRequest $request)
    {
        return Inertia::render('Members/Index', [
            'members' => MemberData::collect(Member::orderBy('name', 'asc')
                ->when($request->get('search'), fn ($query, string $search) => $query->where('name', 'like', "%$search%")->orWhere('known_as', 'like', "%$search%"))
                ->when($request->get('membership_type'), fn ($query, string $membershipType) => $query->membershipType(MembershipType::from($membershipType)))
                ->paginate(25)
                ->appends($request->except('page')
                ), PaginatedDataCollection::class),
            'membershipTypes' => MembershipTypeData::getAll(),
            'filters' => [
                'search' => $request->get('search') ?? null,
                'membershipType' => $request->get('membership_type') ?? null,
            ],
        ]);

    }
}
