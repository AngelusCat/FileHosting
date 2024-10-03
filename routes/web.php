<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FetchRequestController;
use App\Http\Controllers\FileHosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $modifyPassword = bin2hex(random_bytes(5));
    return view('home', ['modifyPassword' => $modifyPassword]);
})->name("files.home");

Route::post('/files', [FileHosting::class, 'upload'])->name('files.upload');

Route::get('/files/{file}', [FileHosting::class, 'show'])->name("files.show");

Route::get('/files/{file}/content', [FileHosting::class, 'download'])->name("files.download");

Route::get('/generatePassword', [FetchRequestController::class, 'generatePassword'])->name("generatePassword");

Route::get('/{file}/password', function (Request $request) {
    $fileId = $request->file;
    return view('password', compact('fileId'));
})->name("password");

Route::post('/{file}/checkPassword', [AuthController::class, 'checkPassword'])->name("checkPassword");

Route::patch('/files/{file}', [FileHosting::class, 'changeMetadata'])->name("files.changeMetadata");

/*Route::get('/testApi', function () {
    $fileName = "welcome.blade.php";
    $contents = file_get_contents("C:/localhost/file/resources/views/welcome.blade.php");
    $status = "public";
    $response = Http::attach(
        'file', $contents, $fileName
    )->post("http://file/api/files");
});*/
