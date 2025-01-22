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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware("permission:".PermissionEnum::PROFILEEDIT->value);
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware("permission:".PermissionEnum::PROFILEEDIT->value);


    Route::get('/members', [Members\MembersIndexController::class, 'index'])->name('members.index')->middleware("permission:".PermissionEnum::VIEWMEMBERS->value);
    Route::get('/member/{member}', [Members\MembersShowController::class, 'show'])->name('member.show')->middleware("permission:".PermissionEnum::VIEWOWNMEMBER->value."|".PermissionEnum::VIEWMEMBERS->value);
    Route::get('/member/create', [Members\MembersCreateController::class, 'create'])->name('member.create')->middleware("permission:".PermissionEnum::CREATEMEMBER->value);
    Route::post('/member', [Members\MembersStoreController::class, 'store'])->name('member.store')->middleware("permission:".PermissionEnum::CREATEMEMBER->value);
    Route::get('/member/{member}/edit', [Members\MembersEditController::class, 'edit'])->name('member.edit')->middleware("permission:".PermissionEnum::EDITMEMBERS->value."|".PermissionEnum::EDITMEMBERS->value);
    Route::patch('/member/{member}', [Members\MembersUpdateController::class, 'update'])->name('member.update')->middleware("permission:".PermissionEnum::EDITMEMBERS->value."|".PermissionEnum::EDITMEMBERS->value);
});

require __DIR__.'/auth.php';
