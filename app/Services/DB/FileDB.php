<?php

namespace App\Services\DB;

use App\Entities\File;
use Illuminate\Support\Facades\DB;

class FileDB
{
    private string $tableName = 'files';
    public function save(File $file): int
    {
        return DB::table($this->tableName)->insertGetId([
            'fake_name' => $file->getFakeName(),
            'original_name' => $file->getOriginalName(),
            'upload_date' => $file->getDate(),
            'description' => $file->getDescription(),
            'size' => $file->getSize(),
            'disk' => $file->getDisk()->name,
            'security_status' => $file->getSecurityStatus()->name,
            'visibility_status' => $file->getVisibilityStatus()->name,
        ]);
    }
}
