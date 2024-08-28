<?php

use App\Http\Controllers\FileHosting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::post('/uploadFile', [FileHosting::class, 'upload']);

Route::get('/downloadFile/{file_id}', [FileHosting::class, 'download']);

Route::get('/generatePassword', function () {
    return response()->json(['password' => bin2hex(random_bytes(5))]);
});
