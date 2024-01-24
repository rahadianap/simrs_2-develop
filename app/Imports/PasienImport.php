<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\MasterData\Entities\Pasien;

class PasienImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pasien([
            // Database column => Excel file column
            'coa_id'     => $row['coa_id'],
            'kode_coa'     => $row['coa_group_id'],
            'nama_coa'     => $row['nama_coa'],
            'level_coa'     => $row['level_akun'],
            'level_nama'     => $row['nama_akun'],
            'coa_header'     => $row['coa_header'],
            'coa_header_id'     => $row['coa_header_id'],
            'nilai_normal'     => $row['coa'],
            'is_valid_coa'     => $row['valid_coa'],
            'is_aktif'     => $row['aktif'],
            'client_id'     => $row['client_id'],
            'created_by'     => $row['createdby'],
        ]);
    }
}
