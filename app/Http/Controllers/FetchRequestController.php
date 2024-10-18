<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Random\RandomException;

class FetchRequestController extends Controller
{
    /**
     * @throws RandomException
     */
    public function generatePassword(): JsonResponse
    {
        return response()->json(['password' => bin2hex(random_bytes(8))]);
    }
}
