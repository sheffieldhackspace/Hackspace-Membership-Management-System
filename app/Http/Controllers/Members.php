<?php

namespace App\Http\Controllers;

use App\Data\MemberData;
use App\Enums\MembershipType;
use App\Http\Requests\Members\MembersFilterRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelData\PaginatedDataCollection;

class Members extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MembersFilterRequest $request)
    {
        $membershipTypes = MembershipType::cases();
        $validated = $request->validated();

//        dd($request);

        return Inertia::render('Members/Index', [
            'members' => MemberData::collect(Member::orderBy('name', 'asc')
                ->when($request->get('search'), fn($query, string $search) => $query->where('name', 'like', "%$search%")->orWhere('known_as', 'like', "%$search%"))
                ->when($request->get('membership_type'), fn($query, string $membershipType) => $query->membershipType(MembershipType::from($membershipType)))
                ->paginate(25)
                ->appends($request->except('page')
                ), PaginatedDataCollection::class),
            'membershipTypes' => $membershipTypes,
            'filters' => [
                'search' => $validated['search'] ?? null,
                'membershipType' => $validated['membership_type'] ?? null,
            ],
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }
}
