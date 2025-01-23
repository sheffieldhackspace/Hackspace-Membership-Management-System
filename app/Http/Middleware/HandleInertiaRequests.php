<?php

namespace App\Http\Middleware;

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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'permissions' => $this->getPermissions($request->user()),
            ],
        ];
    }

    private function getPermissions(?User $user){
        if(!$user){
            return [];
        }

        $permissions = $user->getAllPermissions()->pluck('name');

        $user->members->each(function($member) use (&$permissions){
            $permissions = $permissions->merge($member->getAllPermissions()->pluck('name'));
        });

        return $permissions->unique()->toArray();
    }
}
