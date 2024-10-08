<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/files', [FileHosting::class, 'upload'])->name('api.files.post');

/*
 * POST /files
 * GET /files/id/content
 * HATEOAS http://file/api/files/id/content
 */
