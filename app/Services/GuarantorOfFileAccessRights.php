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
        if ($accessRights === 'private') {
            $fileId = $file->getId();
            $password = $file->getPassword();
            $this->privateFilePasswordsDB->save($fileId, $password);
        }
    }
}
