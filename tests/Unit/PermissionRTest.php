<?php

namespace Tests\Unit;

use App\Entities\File;
use App\Entities\PermissionR;
use App\Enums\ViewingStatus;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class PermissionRTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function viewingStatusIsPublic_permissionIsR(): void
    {
        $request = self::createStub(Request::class);
        $file = self::createStub(File::class);
        $file->method("getViewingStatus")->willReturn(ViewingStatus::public);
        $permissionR = new PermissionR($request, $file);
        self::assertSame("r", $permissionR->getPermission());
    }
}
