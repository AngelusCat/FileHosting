<?php

namespace App\Http\Controllers;

use App\Services\Auth;
use Illuminate\Http\JsonResponse;
use Random\RandomException;
use Illuminate\Http\Request;

class FetchRequestController extends Controller
{
    public function __construct(private Auth $auth){}
    /**
     * @throws RandomException
     */
    public function generatePassword(): JsonResponse
    {
        return response()->json(['password' => bin2hex(random_bytes(8))]);
    }

    public function isUserAuthenticated(Request $request, string $permission, int $fileId): JsonResponse
    {
        return response()->json(['isAuthorized' => $this->auth->isUserAuthenticated($request, $permission, $fileId)]);
    }
}
