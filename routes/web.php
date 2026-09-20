<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppointmentController::class, 'index'])->name('home');

Route::get('/appointments/export', AppointmentExportController::class)->name('appointments.export');
Route::resource('appointments', AppointmentController::class)
    ->only(['index', 'store', 'update', 'destroy']);
