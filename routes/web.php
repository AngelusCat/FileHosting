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

Route::get('test', function () {
    $vt = new \App\Services\VirusTotal();
    dump($vt->getSecurityStatus('17056900752890.png', "C:/localhost/file/storage/app/public/images/17056900752890.png"));
});
