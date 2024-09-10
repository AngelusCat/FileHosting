<?php

use App\Http\Controllers\FileHosting;
use App\Http\Middleware\UserCanViewTheFile;
use App\Services\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name("files.home");

Route::post('/files', [FileHosting::class, 'upload'])->name('files.upload');

Route::get('/files/{file}', [FileHosting::class, 'show'])->name("files.show")->middleware(UserCanViewTheFile::class);

Route::get('/files/{file}/content', [FileHosting::class, 'download'])->name("files.download");

Route::get('/generatePassword', function () {
    return response()->json(['password' => bin2hex(random_bytes(5))]);
})->name("generatePassword");

Route::get('/{file}/viewingPassword', function (Request $request) {
    $fileId = $request->file;
    return view('passwordForPrivateFile', compact('fileId'));
})->name("viewingPassword");

Route::post('/{file}/viewingPassword', [FileHosting::class, 'checkPassword'])->name("viewingPassword.checkPassword");

/*Route::get('/testApi', function () {
    $fileName = "welcome.blade.php";
    $contents = file_get_contents("C:/localhost/file/resources/views/welcome.blade.php");
    $status = "public";
    $response = Http::attach(
        'file', $contents, $fileName
    )->post("http://file/api/files");
});*/

Route::get('/test', function () {
    $fileId = 20;
    $name = "example";
    $factory = new \App\Factories\SimpleFactoryFile(new \App\Services\FilesTDG());
    $file = $factory->createByDB($fileId);
    $originalName = $file->getOriginalName();
    $downloadLink = route("files.download", ["file" => $fileId]);
    return view('test', compact('originalName', 'downloadLink', 'name'));
});
