<?php

namespace App\Http\Middleware;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /* @var User $user */
        $user = $request->user();
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? UserData::fromModel($user) : null,
                'permissions' => $user ? $user->getAllPermissions()->toArray() : [],
            ],
        ];
    }

}
