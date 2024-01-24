<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\MasterData\Entities\Dtd;

class DTDImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Dtd([
            // Database column => Excel file column
            'no_dtd'     => $row['no_dtd'],
            'client_id'     => $row['client_id'],
            'label_dtd'     => $row['label_dtd'],
            'nama_dtd'     => $row['nama_dtd'],
            'is_aktif'     => $row['is_aktif'],
            'created_by'     => $row['created_by'],
            'updated_by'     => $row['updated_by'],
        ]);
    }
}
