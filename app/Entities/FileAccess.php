<?php

namespace App\Entities;

use App\Enums\ViewingStatus;
use App\Services\VisibilityPasswordsTDG;

class FileAccess
{
    private ViewingStatus $viewingStatus;
    private readonly VisibilityPasswordsTDG $visibilityPasswordsTDG;

    public function __construct(ViewingStatus $viewingStatus, string $password = "", ?int $fileId = null)
    {
        if ($viewingStatus->name === "private") {
            $this->installProtection($password, $fileId);
        }
        $this->visibilityPasswordsTDG = new VisibilityPasswordsTDG();
    }
    private function installProtection(string $password, int $fileId): void
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->visibilityPasswordsTDG->save($hash, $fileId);
    }
    public function provideProtection(string $enteredPassword, $fileId): void
    {
        $hash = $this->visibilityPasswordsTDG->getPasswordsByFileId($fileId);
        dump(password_verify($enteredPassword, $hash));
    }
    public function isFilePrivate(): bool
    {
        return $this->viewingStatus->name === "private";
    }
}
