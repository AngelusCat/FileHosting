<?php

namespace App\Services;

use App\Entities\File;
use App\Services\DB\PrivateFilePasswordsDB;

class GuarantorOfFileAccessRights
{
    private PrivateFilePasswordsDB $privateFilePasswordsDB;

    public function __construct(PrivateFilePasswordsDB $privateFilePasswordsDB)
    {
        $this->privateFilePasswordsDB = $privateFilePasswordsDB;
    }
    public function setAccessRights(File $file): void
    {
        $accessRights = $file->getVisibilityStatus()->name;
        $fileId = $file->getId();
        $password = $file->getPassword();
        if ($accessRights === 'private') {
            $this->privateFilePasswordsDB->save($fileId, $password);
        }
    }
}
