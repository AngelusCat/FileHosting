<?php

namespace App\Enums;

enum SecurityStatus
{
    case safe;
    case doubtful;
    case malicious;
}
