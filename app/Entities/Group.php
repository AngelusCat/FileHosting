<?php

namespace App\Entities;

use App\Factories\SimplePasswordFactory;

class Group
{
    public function __construct(private SimplePasswordFactory $passwordFactory){}
    public function makeFileReadableOnlyByGroup(string $password, File $file): void
    {
        $password = $this->passwordFactory->createViewingPassword($file, $password);
        $password->install();
    }

    public function makeFileWritableOnlyByGroup(string $password, File $file): void
    {
        $password = $this->passwordFactory->createModifyPassword($file, $password);
        $password->install();
    }
}
