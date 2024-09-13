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

Route::get('/auth/{file}', function (Request $request, int $fileId) {
    $factory = new \App\Factories\SimpleFactoryFile(new \App\Services\FilesTDG());
    $file = $factory->createByDB($fileId);
    if ($file->getViewingStatus()->name === "public") {
        return response()->json(["success" => true]);
    }
    if (empty($request->cookie("jwt"))) {
        return response()->json(["success" => false]);
    } else {
        $jwtAuth = new JWTAuth();
        $jwt = $jwtAuth->getJwtFromStringRepresentation($request->cookie("jwt"));
        $fileIdFromPayload = $jwt->getDecoratedPayload()["file_id"];
        if ($jwtAuth->validateJWT($jwt) === true && $fileIdFromPayload === $fileId) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false]);
        }
    }
});
