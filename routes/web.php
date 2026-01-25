<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/notes', function () {
        return view('notes');
    })->name('notes');

    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/auth/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/auth/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/auth/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/auth/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});
