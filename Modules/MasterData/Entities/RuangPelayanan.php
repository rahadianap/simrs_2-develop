<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuangPelayanan extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $connection = 'dbclient';

    protected $table = 'tb_ruang';
    protected $primaryKey = 'ruang_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [
        'ruang_id',
        'unit_id',
        'ruang_nama',
        'is_utama',
        'kelas_standar',
        'harga_id',
        'lokasi',
        'deskripsi',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\RuangPelayananFactory::new();
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
