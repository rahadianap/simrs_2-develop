<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokterUnit extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $incrementing = false;
    protected $connection = 'dbclient';
    protected $table = 'tb_dokter_unit';
    protected $primaryKey = 'dokter_unit_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [
        'dokter_unit_id',
        'dokter_id',
        'unit_id',
        'ruang_id',
        'prefix_no_antrian',
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
        return \Modules\MasterData\Database\factories\DokterUnitFactory::new();
    }
}
