<?php

namespace App\ValueObjects;

use App\Enums\VisibilityStatus;

class Visibility
{
    private VisibilityStatus $visibilityStatus;
    private ?string $password = NULL;

    public function __construct(string $customVisibilityStatus, ?string $password = NULL)
    {
        $this->visibilityStatus = ($customVisibilityStatus === 'public') ? VisibilityStatus::public : VisibilityStatus::private;
        $this->password = $password;
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
