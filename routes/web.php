<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;

Route::middleware(['guest:karyawan'])->group(function () {
   Route::get('/login', function () {
    return view('auth.login');
    })->name('login');
Route::post('/login',[AuthController::class, 'login'])->name('login');      
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout',[AuthController::class, 'logout'])->name('logout');

    Route::get('/presensi/create',[PresensiController::class, 'create'])->name('/presensi/create');
    Route::post('/presensi/store',[PresensiController::class, 'store'])->name('/presensi/store');
});