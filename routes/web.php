<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoCheckerController;

Route::get('/', [SeoCheckerController::class, 'index'])->name('home');
Route::post('/analyze', [SeoCheckerController::class, 'analyze'])->name('analyze');
