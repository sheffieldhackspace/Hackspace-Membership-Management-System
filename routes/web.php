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
    Route::get('/members/{member}', [Members\MembersShowController::class, 'show'])->name('members.show')->middleware("permission:".PermissionEnum::VIEWOWNMEMBER->value."|".PermissionEnum::VIEWMEMBERS->value);
    Route::get('/members/create', [Members\MembersCreateController::class, 'create'])->name('members.create')->middleware("permission:".PermissionEnum::CREATEMEMBER->value);
    Route::post('/members', [Members\MembersStoreController::class, 'store'])->name('members.store')->middleware("permission:".PermissionEnum::CREATEMEMBER->value);
    Route::get('/members/{member}/edit', [Members\MembersEditController::class, 'edit'])->name('members.edit')->middleware("permission:".PermissionEnum::EDITMEMBERS->value."|".PermissionEnum::EDITMEMBERS->value);
    Route::patch('/members/{member}', [Members\MembersUpdateController::class, 'update'])->name('members.update')->middleware("permission:".PermissionEnum::EDITMEMBERS->value."|".PermissionEnum::EDITMEMBERS->value);
});

require __DIR__.'/auth.php';
