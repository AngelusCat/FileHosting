<?php

namespace App\Services;

use App\Entities\File;
use App\Enums\ApiRequestStatus;
use Illuminate\Http\JsonResponse;

class JsonResponseHelper
{
    private array $links;

    public function __construct()
    {
        $this->links = [
            'metadata' => "http://file/api/files/%d/metadata",
            'auth' => "http://file/api/auth/%d",
            'content' => "http://file/api/files/%d/content",
            'update' => "http://file/api/files/%d"
        ];
    }
    public function getSuccessfulResponseForChangeMetadata(int $fileId): JsonResponse
    {
        return response()->json([
            'status' => ApiRequestStatus::success->name,
            'data' => [
                'links' => [
                    'metadata' => sprintf($this->links['metadata'], $fileId)
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
                "links" => [
                    'auth' => sprintf($this->links['auth'], $fileId)
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
            'data' => compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus') + ['links' => [
                    'content' => sprintf($this->links['content'], $fileId),
                    'update' => sprintf($this->links['update'], $fileId)
                ]]
        ]);
    }

    public function getSuccessfulResponseForDownload(int $fileId, string $path): JsonResponse
    {
        return response()->json([
            'status' => ApiRequestStatus::success->name,
            'data' => [
                'content' => mb_convert_encoding(file_get_contents($path), 'utf8', 'UTF-8'),
                'links' => [
                    'metadata' => sprintf($this->links['metadata'], $fileId)
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
                    'metadata' => sprintf($this->links['metadata'], $fileId),
                    'content' => sprintf($this->links['content'], $fileId)
                ]
            ]
        ]);
    }
}
