<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Factories\SimpleFactoryFile;
use Illuminate\Http\JsonResponse;
use Random\RandomException;
use Illuminate\Http\Request;

class FetchRequestController extends Controller
{
    public function __construct(private SimpleFactoryFile $fileFactory){}
    /**
     * @throws RandomException
     */
    public function generatePassword(): JsonResponse
    {
        return response()->json(['password' => bin2hex(random_bytes(8))]);
    }

    public function canCurrentUserChangeFileMetadata(Request $request, int $fileId): JsonResponse
    {
//        return response()->json(['isAuthorized' => $this->auth->isUserAuthenticated($request, $permission, $fileId)]);
        $file = $this->fileFactory->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canWrite() === false) {
            return response()->json(['message' => 'Permission denied'], 403);
        }
        return response()->json(['message' => 'Permission succeeded']);
    }
}
