<?php

namespace App\Http\Controllers;

use App\Enums\ApiRequestStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function changeMetadata(int $fileId): JsonResponse
    {
        return response()->json([
            'status' => ApiRequestStatus::success->name,
            'data' => [
                'link' => [
                    'metadata' => "http://file/api/files/$fileId/metadata"
                ]
            ]
        ]);
    }
}
