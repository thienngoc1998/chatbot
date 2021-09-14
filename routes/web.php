<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bot',[\App\Http\Controllers\BotController::class, 'bot'])->middleware('verifybot');
Route::post('/bot',[\App\Http\Controllers\BotController::class, 'bot']);
