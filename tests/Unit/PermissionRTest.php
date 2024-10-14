<?php

namespace Tests\Unit;

use App\Entities\File;
use App\Entities\PermissionR;
use App\Enums\ViewingStatus;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class PermissionRTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function viewingStatusIsPublic_permissionIsR(): void
    {
        $file = self::createStub(File::class);
        $file->method("getViewingStatus")->willReturn(ViewingStatus::public);
        $request = self::createStub(Request::class);
        $permissionR = new PermissionR($request, $file);
        self::assertSame("r", $permissionR->getPermission());
    }

    #[Test]
    public function viewingStatusIsPrivate_isUserAuthenticatedReturnsTrue_permissionIsR(): void
    {
        $file = self::createConfiguredStub(File::class, [
            'getViewingStatus' => ViewingStatus::private,
            'getId' => 1
        ]);
        $jwt = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmaWxlX2lkIjoxLCJwZXJtaXNzaW9ucyI6InJlYWRvbmx5In0=.576379177206ef4c013ad106b99ebc281b4b51c08d3831f503997778b6a4a983";
        $request = self::createStub(Request::class);
        $request->expects($this->any())->method("hasCookie")->willReturn(true);
        $request->expects($this->any())->method("cookie")->willReturn($jwt);
        $permissionR = new PermissionR($request, $file);
        self::assertSame("r", $permissionR->getPermission());
    }

    #[Test]
    public function viewingStatusIsPrivate_isUserAuthenticatedReturnsFalse_permissionIsDash(): void
    {
        $file = self::createConfiguredStub(File::class, [
            'getViewingStatus' => ViewingStatus::private,
            'getId' => 1
        ]);
        $request = self::createStub(Request::class);
        $request->expects($this->any())->method("hasCookie")->willReturn(false);
        $permissionR = new PermissionR($request, $file);
        self::assertSame("-", $permissionR->getPermission());
    }
}
