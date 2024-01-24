<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokterJadwal extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $connection = 'dbclient';
    protected $table = 'tb_dokter_jadwal';
    protected $primaryKey = 'jadwal_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [
        'jadwal_id',
        'dokter_unit_id',
        'hari',
        'mulai',
        'selesai',
        'is_ext_app',
        'kuota_online',
        'kuota',
        'interval_periksa',
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
        return \Modules\MasterData\Database\factories\DokterJadwalFactory::new();
    }
}
