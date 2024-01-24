<?php

namespace Modules\Pendaftaran\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftaran extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_registrasi';
    public $incrementing = false;
    protected $primaryKey = 'reg_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [
        'reg_id',
        'tgl_registrasi',
        'jns_registrasi',
        'cara_registrasi',
        'tgl_periksa',
        'kode_booking',
        'no_antrian',
        'jadwal_id',
        'dokter_id',
        'unit_id',
        'ruang_id',
        'bed_id',
        'asal_pasien',
        'ket_asal_pasien',
        'pasien_id',
        'jns_penjamin',
        'penjamin_id',
        'nama_penanggung',
        'hub_penanggung',
        'is_bpjs',
        'status_reg',
        'status_pulang',
        'cara_pulang',
        'tgl_pulang',
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
        return \Modules\Pendaftaran\Database\factories\PendaftaranFactory::new();
    }
}
