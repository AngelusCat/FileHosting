<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use App\Enums\VisibilityStatus;
use App\Interfaces\Antivirus;
use App\Services\VirusTotal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class File
{
    private string $fakeName;
    private string $originalName;
    private string $date;
    private string $description;
    private int $size;
    private Disk $disk;
    private SecurityStatus $securityStatus;
    private VisibilityStatus $visibilityStatus;
    private string $content;

    public function __construct(Request $request, Antivirus $antivirus = new VirusTotal())
    {
        $file = $request->file('file');
        $this->fakeName = preg_split('/\.[A-Za-z0-9]{1,4}$/', $file->hashName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $this->originalName = preg_replace('/ /', '_', $file->getClientOriginalName());
        $this->date = now();
        $this->description = trim($request->input('description') ?? '');
        $this->size = $file->getSize();
        $mimeType = [];
        preg_match('/image/', $file->getMimeType(), $mimeType);
        $mimeType = $mimeType[0] ?? '';
        $this->disk = ($mimeType === 'image') ? Disk::public : Disk::local;
        $this->securityStatus = $antivirus->check();
        $this->visibilityStatus = ($request->input('viewingStatus') === 'public') ? VisibilityStatus::public : VisibilityStatus::private;
        $this->content = $file->getContent();
    }

    /**
     * @return string
     */
    public function getFakeName(): string
    {
        return $this->fakeName;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return Disk
     */
    public function getDisk(): Disk
    {
        return $this->disk;
    }

    /**
     * @return SecurityStatus
     */
    public function getSecurityStatus(): SecurityStatus
    {
        return $this->securityStatus;
    }

    /**
     * @return VisibilityStatus
     */
    public function getVisibilityStatus(): VisibilityStatus
    {
        return $this->visibilityStatus;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function isSecretPathUsed(): bool
    {
        return $this->disk === Disk::local;
    }

    public function getSaveName(): string
    {
        return ($this->disk === Disk::local) ? $this->fakeName : $this->originalName;
    }
}
