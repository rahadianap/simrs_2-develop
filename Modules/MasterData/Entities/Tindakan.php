<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tindakan extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_tindakan';

    protected $primaryKey = 'tindakan_id';

    public $incrementing = false;

    protected $fillable = [
        'tindakan_id',
        'tindakan_nama',
        'is_paket',
        'is_diskon',
        'is_cito',
        'is_hitung_admin',
        'is_tampilkan_dokter',
        'is_aktif',
        'client_id',
        'created_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];
    // protected $hidden = ['created_at','updated_at','deleted_at'];

    public function kelompok_tindakan() {
        return $this->hasOne(KelompokTindakan::class,'kelompok_tindakan_id','kelompok_tindakan_id');
    }

    public function kelompok_tagihan() {
        return $this->hasOne(KelompokTagihan::class,'kelompok_tagihan_id','kelompok_tagihan_id');
    }

    public function kelompok_vclaim() {
        return $this->hasOne(KelompokVclaim::class,'kelompok_vclaim_id','kelompok_vclaim_id');
    }
}
