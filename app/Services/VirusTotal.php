<?php

namespace App\Services;

use App\Entities\File;
use App\Enums\SecurityStatus;
use App\Interfaces\Antivirus;

class VirusTotal implements Antivirus
{

    public function check(): SecurityStatus
    {
        return SecurityStatus::safe;
    }
}
