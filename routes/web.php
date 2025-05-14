<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::any('login-process', [AuthController::class, 'loginprocess'])->name('login.process');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [BookingController::class, 'index'])->name('booking.form');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
});