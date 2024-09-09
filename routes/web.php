<?php

use App\Entities\JWT;
use App\Http\Controllers\FileHosting;
use App\Services\JWTAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::post('/uploadFile', [FileHosting::class, 'upload']);

Route::get('/downloadFile/{file_id}', [FileHosting::class, 'download']);

Route::get('/generatePassword', function () {
    return response()->json(['password' => bin2hex(random_bytes(5))]);
});

Route::get('/testApi', function () {
    $fileName = "welcome.blade.php";
    $contents = file_get_contents("C:/localhost/file/resources/views/welcome.blade.php");
    $status = "public";
    $response = Http::attach(
        'file', $contents, $fileName
    )->post("http://file/api/files");
});

Route::get('show/{file_id}', [FileHosting::class, 'show'])->middleware(\App\Http\Middleware\UserCanViewTheFile::class);

Route::get('/{file_id}/privatePassword', function (\Illuminate\Http\Request $request) {
    $fileId = $request->file_id;
    return view('passwordForPrivateFile', compact('fileId'));
});

Route::post('/{file_id}/checkPassword', [FileHosting::class, 'checkPassword']);
