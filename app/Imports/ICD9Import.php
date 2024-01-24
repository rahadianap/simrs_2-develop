<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\MasterData\Entities\Icd9;

class ICD9Import implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Icd9([
            // Database column => Excel file column
            'kode_icd'     => $row['kode_icd'],
            'client_id'     => $row['client_id'],
            'nama_icd'     => $row['nama_icd'],
            'kata_kunci'     => $row['kata_kunci'],
            'is_valid_icd'     => $row['is_valid_icd'],
            'is_aktif'     => $row['is_aktif'],
            'created_by'     => $row['created_by'],
            'updated_by'     => $row['updated_by'],
        ]);
    }
}
