<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

//Route::get('/', function () {
//    return view('welcome');
//});


// TODO: create bookstore view  route


Route::get('/',
    [SeoCheckerController::class, 'index'])->name('home');

Route::post('/analyze', [
    SeoCheckerController::class, 'analyze'
])->name('analyze');
