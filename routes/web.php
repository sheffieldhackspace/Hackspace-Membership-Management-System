<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Members;
use App\Http\Controllers\UserController;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/user/{user}', [UserController::class, 'edit'])->name('user.edit')->can('update', 'user');
    Route::patch('/user/{user}', [UserController::class, 'update'])->name('user.update')->can('update', 'user');

    Route::get('/members', [Members\MembersIndexController::class, 'index'])->name('members.index')->can('viewAny', Member::class);
    Route::get('/member/{member}', [Members\MemberShowController::class, 'show'])->name('member.show')->can('view', 'member');
    Route::get('/member/create', [Members\MemberCreateController::class, 'create'])->name('member.create')->can('create', Member::class);
    Route::post('/member', [Members\MemberStoreController::class, 'store'])->name('member.store')->can('create', Member::class);
    Route::get('/member/{member}/edit', [Members\MemberEditController::class, 'edit'])->name('member.edit')->can('update', 'member');
    Route::patch('/member/{member}', [Members\MemberUpdateController::class, 'update'])->name('member.update')->can('update', 'member');

    Route::get('/admin/discord/connect', [Admin\DiscordAdminController::class, 'connect'])->name('discord.connect')->middleware('permission:'.PermissionEnum::ADMINISTERDISCORD->value);

});

require __DIR__.'/auth.php';
