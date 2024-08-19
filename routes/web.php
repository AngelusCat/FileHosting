<?php

use App\Enums\Disk;
use App\Test\FilesTDG;
use Illuminate\Support\Facades\Route;

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

/*Route::get('/test', function (FilesTDG $tdg) {
    $tdg->save(Disk::public, 'example.png', 'example.png');
});

Route::get('/showtest', function (FilesTDG $tdg) {
    $data = $tdg->findById(1);
    dump($data);
    dump($data->disk);
});*/

Route::get('/generatePassword', function () {
    return response()->json(['password' => bin2hex(random_bytes(5))]);
});
