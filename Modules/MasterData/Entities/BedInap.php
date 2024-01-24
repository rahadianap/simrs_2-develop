<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BedInap extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_bed';
    protected $primaryKey = 'bed_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [
        'bed_id',
        'ruang_id',
        'no_bed',
        'jns_kelamin_bed',
        'pasien_id',
        'reg_id',
        'trx_id',
        'tgl_masuk',
        'status',
        'is_tersedia',
        'tgl_rencana_pulang',
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
        return \Modules\MasterData\Database\factories\BedPelayananFactory::new();
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
