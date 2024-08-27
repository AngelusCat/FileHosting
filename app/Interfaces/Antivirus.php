<?php

namespace App\Interfaces;

use App\Entities\File;

interface Antivirus
{
    public function check(string $name, string $content);
}
