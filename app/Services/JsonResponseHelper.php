<?php

namespace App\Services;

use App\Entities\File;
use App\Enums\ApiRequestStatus;
use Illuminate\Http\JsonResponse;

class JsonResponseHelper
{
    public function getSuccessfulResponseForChangeMetadata(int $fileId): JsonResponse
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

    public function getResponseUserIsNotAuthorized(int $fileId): JsonResponse
    {
        return response()->json([
            'status' => ApiRequestStatus::fail->name,
            'data' => [
                "message" => "Ошибка авторизации",
                "link" => [
                    "auth" => "http://file/api/auth/$fileId"
                ]
            ]
        ]);
    }

    public function getSuccessfulResponseForShow(File $file): JsonResponse
    {
        $fileId = $file->getId();
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $file->getOriginalName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $size = $file->getSize();
        $uploadDate = $file->getUploadDate();
        $description = $file->getDescription();
        $securityStatus = $file->getSecurityStatus()->value;
        return response()->json([
            'status' => ApiRequestStatus::success->name,
            'data' => compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus') + ['link' => [
                    'content' => "http://file/api/files/$fileId/content",
                    'update' => "http://file/api/files/$fileId"
                ]
                ]
        ]);
    }

    public function getSuccessfulResponseForDownload(int $fileId, string $path): JsonResponse
    {
        return response()->json([
            'status' => ApiRequestStatus::success->name,
            'data' => [
                'content' => mb_convert_encoding(file_get_contents($path), 'utf8', 'UTF-8'),
                'link' => [
                    'metadata' => "http://file/api/files/$fileId/metadata"
                ]
            ]
        ]);
    }

    public function getSuccessfulResponseForUpload(string $modifyPassword, int $fileId): JsonResponse
    {
        return response()->json([
            'status' => ApiRequestStatus::success->name,
            'data' => [
                'modifyPassword' => $modifyPassword,
                'links' => [
                    'metadata' => "http://file/api/files/$fileId/metadata",
                    'content' => "http://file/api/files/$fileId/content"
                ]
            ]
        ]);
    }
}
