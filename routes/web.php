<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FetchRequestController;
use App\Http\Controllers\FileHosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name("files.home");

Route::post('/files', [FileHosting::class, 'upload'])->name('files.upload');

Route::get('/files/{file}', [FileHosting::class, 'show'])->name("files.show");

Route::get('/files/{file}/content', [FileHosting::class, 'download'])->name("files.download");

Route::get('/generatePassword', [FetchRequestController::class, 'generatePassword'])->name("generatePassword");

Route::get('/{file}/viewingPassword', function (Request $request) {
    $fileId = $request->file;
    return view('passwordR', compact('fileId'));
})->name("viewingPassword");

Route::get('/{file}/modifyPassword', function (Request $request) {
    $fileId = $request->file;
    return view('passwordW', compact('fileId'));
})->name("modifyPassword");

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
