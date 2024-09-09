<?php

use App\Http\Controllers\FileHosting;
use App\Http\Middleware\UserCanViewTheFile;
use Illuminate\Support\Facades\Route;

Route::get('/files/create', function () {
    return view('home');
});

Route::post('/files', [FileHosting::class, 'upload']);

Route::get('/files/{file}', [FileHosting::class, 'show'])->middleware(UserCanViewTheFile::class);

Route::get('/files/{file}/content', [FileHosting::class, 'download']);

Route::get('/generatePassword', function () {
    return response()->json(['password' => bin2hex(random_bytes(5))]);
});

Route::get('/viewingPassword/', function (\Illuminate\Http\Request $request) {
    $fileId = $request->file_id;
    return view('passwordForPrivateFile', compact('fileId'));
});

Route::post('/{file_id}/checkPassword', [FileHosting::class, 'checkPassword']);





Route::get('/', function () {
    return view('home');
})->name('home');
Route::post('/uploadFile', [FileHosting::class, 'upload'])->name('uploadFile');

Route::get('/downloadFile/{file_id}', [FileHosting::class, 'download'])->name('downloadFile');

Route::get('/generatePassword', function () {
    return response()->json(['password' => bin2hex(random_bytes(5))]);
})->name('generatePassword');

/*Route::get('/testApi', function () {
    $fileName = "welcome.blade.php";
    $contents = file_get_contents("C:/localhost/file/resources/views/welcome.blade.php");
    $status = "public";
    $response = Http::attach(
        'file', $contents, $fileName
    )->post("http://file/api/files");
});*/

Route::get('show/{file_id}', [FileHosting::class, 'show'])->middleware(UserCanViewTheFile::class);

Route::get('/{file_id}/privatePassword', function (\Illuminate\Http\Request $request) {
    $fileId = $request->file_id;
    return view('passwordForPrivateFile', compact('fileId'));
});

Route::post('/{file_id}/checkPassword', [FileHosting::class, 'checkPassword']);
