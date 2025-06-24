<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

// Main attendance page
Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.form');

// API Routes for AJAX calls
Route::prefix('api')->group(function () {
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('api.attendance.store');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('api.attendance.history');
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('api.attendance.destroy');
    Route::get('/attendance/statistics', [AttendanceController::class, 'statistics'])->name('api.attendance.statistics');
});