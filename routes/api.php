<?php

use App\Http\Controllers\API\DiscordUserSearchAPIController;
use App\Models\Member;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->name('api.')
    ->prefix('api')
    ->group(function () {
        Route::get('/members/search', [DiscordUserSearchAPIController::class, 'search'])->name('discord-members.search')->can('viewAny', Member::class);
    });
