<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use App\Enums\VisibilityStatus;
use App\Interfaces\Antivirus;
use App\Services\VirusTotal;
use App\ValueObjects\Visibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class File
{
    private ?int $id = NULL;
    private ?string $fakeName;
    private string $originalName;
    private string $date;
    private string $description;
    private int $size;
    private Disk $disk;
    private SecurityStatus $securityStatus;
    private Visibility $visibility;
    private string $content;

    public function __construct(Request $request, Antivirus $antivirus = new VirusTotal())
    {
        $file = $request->file('file');
        $mimeType = [];
        preg_match('/image/', $file->getMimeType(), $mimeType);
        $mimeType = $mimeType[0] ?? '';
        $this->disk = ($mimeType === 'image') ? Disk::public : Disk::local;
        $this->fakeName = ($this->disk->name === 'local') ? preg_split('/\.[A-Za-z0-9]{1,4}$/', $file->hashName(), -1, PREG_SPLIT_NO_EMPTY)[0] : NULL;
        $this->originalName = preg_replace('/ /', '_', $file->getClientOriginalName());
        $this->date = now();
        $this->description = trim($request->input('description') ?? '');
        $this->size = $file->getSize();
        $this->securityStatus = $antivirus->check();
        $this->visibility = new Visibility($request->input('viewingStatus'), $request->input('visibilityPassword'));
        $this->content = $file->getContent();
    }

    public function setAccessRights(): void
    {
        $this->visibility->setAccessRights($this->id);
    }

    /**
     * @return string
     */
    public function getFakeName(): ?string
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

    public function getPassword(): string
    {
        return $this->visibility->getPassword();
    }

    /**
     * @return VisibilityStatus
     */
    public function getVisibilityStatus(): VisibilityStatus
    {
        return $this->visibility->getVisibilityStatus();
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

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
