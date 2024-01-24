<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\MasterData\Entities\Coa;

class CoaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Coa([
            // Database column => Excel file column
            'coa_id'     => $row['coa_id'],
            'kode_coa'     => $row['kode_coa'],
            'nama_coa'     => $row['nama_coa'],
            'level_coa'     => $row['level_coa'],
            'level_nama'     => $row['level_nama'],
            'coa_header'     => $row['coa_header'],
            'coa_header_id'     => $row['coa_header_id'],
            'nilai_normal'     => $row['nilai_normal'],
            'text_coa'     => $row['text_coa'],
            'is_valid_coa'     => $row['is_valid_coa'],
            'is_aktif'     => $row['is_aktif'],
            'client_id'     => $row['client_id'],
            'created_by'     => $row['created_by'],
            'updated_by'     => $row['updated_by'],
        ]);
    }
}
