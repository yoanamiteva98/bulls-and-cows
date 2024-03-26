<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [GameController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [GameController::class, 'index'])->name('dashboard');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});


Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);
Route::get('/start-game', [GameController::class, 'startGame'])->name('start-game');
Route::post('/guess', [GameController::class, 'guess'])->name('guess');
Route::post('/give-up', [GameController::class, 'giveUp'])->name('give-up');



