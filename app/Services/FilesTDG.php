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
        return DB::table($this->tableName)->insertGetId($this->getListOfFieldsByVariableNames($data));
    }

    public function findById(int $id)
    {
        return DB::table($this->tableName)->find($id, ['id', 'disk', 'name_to_save', 'original_name', 'security_status', 'upload_date', 'description', 'size', 'viewing_status']);
    }

    public function getViewingStatus(int $id): string
    {
        return DB::table($this->tableName)->where('id', $id)->value('viewing_status');
    }

    public function update(int $id, array $data): void
    {
        DB::table($this->tableName)->where('id', $id)->update($this->getListOfFieldsByVariableNames($data));
    }

    private function getListOfFieldsByVariableNames(array $variableNames): array
    {
        foreach ($variableNames as $variableName => $variableValue) {
            $symbols = preg_split('//', $variableName, -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0; $i < count($symbols); $i++) {
                if (ctype_upper($symbols[$i]) && $i !== 0) {
                    $newSymbols[] = '_';
                    $newSymbols[] = mb_strtolower($symbols[$i]);
                } else {
                    $newSymbols[] = $symbols[$i];
                }
            }
            $listOfFieldsToSave[implode('', $newSymbols)] = $variableValue;
            $newSymbols = [];
        }
        return $listOfFieldsToSave;
    }
}
