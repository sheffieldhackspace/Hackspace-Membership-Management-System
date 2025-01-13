<?php

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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/members', [Members\MembersIndexController::class, 'index'])->name('members.index');
    Route::get('/members/create', [Members\MembersCreateController::class, 'create'])->name('members.create');
    Route::get('/members/{member}/edit', [Members\MembersEditController::class, 'edit'])->name('members.edit');
    Route::get('/members/{member}', [Members\MembersShowController::class, 'show'])->name('members.show');
    Route::post('/members', [Members\MembersStoreController::class, 'store'])->name('members.store');
    Route::patch('/members/{member}', [Members\MembersUpdateController::class, 'update'])->name('members.update');
});



require __DIR__.'/auth.php';
