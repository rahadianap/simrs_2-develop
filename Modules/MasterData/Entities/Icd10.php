<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Icd10 extends Model
{
    use HasFactory;

    protected $connection = 'dbclient';

    protected $fillable = [
        'kode_icd',
        'client_id',
        'nama_icd',
        'no_dtd',
        'kata_kunci',
        'is_valid_icd',
        'is_aktif',
        'created_by',
        'updated_by'
    ];

    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_icd10';
    protected $primaryKey = 'kode_icd';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
