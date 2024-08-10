<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
    return view('home');
});
Route::post('/uploadFile', [\App\Http\Controllers\FileHostingController::class, 'uploadFile']);

Route::get('/test', function () {
    //
});

Route::get('/generatePassword', function () {
    return response()->json(['password' => bin2hex(random_bytes(5))]);
});
