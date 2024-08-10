<?php

namespace App\ValueObjects;

use App\Enums\VisibilityStatus;
use App\Services\DB\PrivateFilePasswordsDB;

class Visibility
{
    private VisibilityStatus $visibilityStatus;
    private ?string $password = NULL;
    private PrivateFilePasswordsDB $privateFilePasswordsDB;

    public function __construct(PrivateFilePasswordsDB $privateFilePasswordsDB, string $customVisibilityStatus, ?string $password = NULL)
    {
        $this->visibilityStatus = ($customVisibilityStatus === 'public') ? VisibilityStatus::public : VisibilityStatus::private;
        $this->privateFilePasswordsDB = $privateFilePasswordsDB;
        $this->password = $password;
    }

    public function setAccessRights(int $fileId): void
    {
        if ($this->visibilityStatus === VisibilityStatus::private) {
            $this->privateFilePasswordsDB->save($fileId, $this->password);
        }
    }

    public function getVisibilityStatus(): VisibilityStatus
    {
        return $this->visibilityStatus;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
