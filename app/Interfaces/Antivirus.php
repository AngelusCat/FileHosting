<?php

namespace App\Interfaces;

use App\Entities\File;
use App\Enums\SecurityStatus;

interface Antivirus
{
    public function check(): SecurityStatus;
}
