<?php

use App\Http\Controllers\CapacityCalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CapacityCalculatorController::class, 'index'])->name('calculator.index');
Route::post('/api/sync', [CapacityCalculatorController::class, 'sync'])->name('calculator.sync');
Route::post('/reset', [CapacityCalculatorController::class, 'reset'])->name('calculator.reset');
