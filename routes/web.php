<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentExportController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\DailyNoteController;
use App\Http\Controllers\PendingTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppointmentController::class, 'index'])->name('home');

Route::get('/appointments/export', AppointmentExportController::class)->name('appointments.export');
Route::resource('appointments', AppointmentController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('daily-notes', DailyNoteController::class)
    ->only(['store', 'update', 'destroy']);
Route::post('/daily-notes/{dailyNote}/pending-tasks', [PendingTaskController::class, 'store'])->name('pending-tasks.store');
Route::patch('/pending-tasks/{pendingTask}/move', [PendingTaskController::class, 'move'])->name('pending-tasks.move');
Route::patch('/pending-tasks/{pendingTask}', [PendingTaskController::class, 'update'])->name('pending-tasks.update');
Route::delete('/pending-tasks/{pendingTask}', [PendingTaskController::class, 'destroy'])->name('pending-tasks.destroy');
Route::post('/pending-tasks/{pendingTask}/checklist-items', [ChecklistItemController::class, 'store'])->name('checklist-items.store');
Route::patch('/checklist-items/{checklistItem}', [ChecklistItemController::class, 'update'])->name('checklist-items.update');
Route::delete('/checklist-items/{checklistItem}', [ChecklistItemController::class, 'destroy'])->name('checklist-items.destroy');
