<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileHosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/files', [FileHosting::class, 'upload'])->name('api.files.post');

Route::get('/files/{id}/metadata', [FileHosting::class, 'show'])->name('api.files.metadata');

Route::get('/files/{id}/content', function ($id) {
    return response()->json([
        'id' => $id
    ]);
});

Route::post('/auth/{id}', [AuthController::class, 'checkPassword'])->name('api.auth.post');
