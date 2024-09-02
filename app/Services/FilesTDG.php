<?php

namespace App\Services;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use App\Enums\ViewingStatus;
use Illuminate\Support\Facades\DB;

class FilesTDG
{
    private string $tableName = 'files';

    public function save(array $data): int
    {
        /*return DB::table($this->tableName)->insertGetId([
            'disk' => $disk->name,
            'name_to_save' => $nameToSave,
            'original_name' => $originalName,
            'security_status' => $securityStatus->name,
            'size' => $size,
            'upload_date' => $uploadDate,
            'description' => $description,
            'viewing_status' => $viewingStatus->name
        ]);*/

        return DB::table($this->tableName)->insertGetId($data);
    }

    public function findById(int $id)
    {
        return DB::table($this->tableName)->find($id, ['id', 'disk', 'name_to_save', 'original_name', 'security_status', 'upload_date', 'description', 'size', 'viewing_status']);
    }

    public function getViewingStatus(int $id): string
    {
        return DB::table($this->tableName)->where('id', $id)->value('viewing_status');
    }

    public function getListOfFieldsToSaveByVariableNames(array $variableNames): array
    {
        foreach ($variableNames as $variableName => $variableValue) {
            $variableNameInString = substr($variableName, 1);
            $symbols = preg_split('//', $variableNameInString, -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0; $i < count($symbols); $i++) {
                if (ctype_upper($symbols[$i]) && $i !== 0) {
                    $newSymbols[] = '_';
                    $newSymbols[] = mb_strtolower($symbols[$i]);
                } else {
                    $newSymbols[] = $symbols[$i];
                }
            }
            $listOfFieldsToSave[] = implode('', $newSymbols);
            $newSymbols = [];
        }
        $values = array_values($variableNames);
        return array_combine($listOfFieldsToSave, $values);
    }
}
