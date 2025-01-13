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

    Route::get('/members', [Members::class, 'index'])->name('members.index');
    Route::get('/members/create', [Members::class, 'create'])->name('members.create');
    Route::post('/members', [Members::class, 'store'])->name('members.store');
    Route::get('/members/{member}/edit', [Members::class, 'edit'])->name('members.edit');
    Route::patch('/members/{member}', [Members::class, 'update'])->name('members.update');

});



require __DIR__.'/auth.php';
