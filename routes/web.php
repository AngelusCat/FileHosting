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

Route::patch('/files/{file}', [FileHosting::class, 'changeMetadata'])->name("files.changeMetadata");

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
    $factory = new \App\Factories\SimpleFactoryFile(new \App\Services\FilesTDG());
    $file = $factory->createByDB($fileId);
    $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $file->getOriginalName(), -1, PREG_SPLIT_NO_EMPTY)[0];
    $size = $file->getSize();
    $uploadDate = $file->getUploadDate();
    $description = $file->getDescription();
    $securityStatus = $file->getSecurityStatus()->value;
    $downloadLink = route("files.download", ["file" => $fileId]);
    $csrfToken = csrf_token();
    return view('test', compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus', 'downloadLink', 'csrfToken', 'fileId'));
});

Route::get('/test1', function () {
    $name = "Александра.php";
    dump(preg_match_all("/\.[a-zA-Z]{1,}$/", $name, $result));
    dump($result);
});
