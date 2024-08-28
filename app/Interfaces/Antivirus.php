<?php

namespace App\Interfaces;

use App\Entities\File;
use App\Enums\SecurityStatus;

interface Antivirus
{
    public function getSecurityStatus(string $fileName, string $fileContent): SecurityStatus;
}
