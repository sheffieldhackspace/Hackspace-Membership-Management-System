<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\Members;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware("can:edit,user");
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware("can:edit,user");

    Route::get('/members', [Members\MembersIndexController::class, 'index'])->name('members.index')->middleware('can:viewAny,member');
    Route::get('/member/{member}', [Members\MemberShowController::class, 'show'])->name('member.show')->middleware('can:view,member');
    Route::get('/member/create', [Members\MemberCreateController::class, 'create'])->name('member.create')->middleware('can:create,member');
    Route::post('/member', [Members\MemberStoreController::class, 'store'])->name('member.store')->middleware('can:create,member');
    Route::get('/member/{member}/edit', [Members\MemberEditController::class, 'edit'])->name('member.edit')->middleware('can:update,member');
    Route::patch('/member/{member}', [Members\MemberUpdateController::class, 'update'])->name('member.update')->middleware('can:update,member');
});

require __DIR__.'/auth.php';
