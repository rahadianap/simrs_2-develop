<?php

namespace Modules\Penunjang\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersalinanBayi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_persalinan_bayi';
    protected $primaryKey = 'persalinan_bayi_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $fillable = [
        'persalinan_bayi_id',
        'persalinan_id',
        'reg_id',
        'trx_id',
        'sub_trx_id',
        'no_rm_bayi',
        'tgl_kelahiran',
        'jam_kelahiran',
        'umur_kehamilan',
        'jenis_persalinan',
        'kelahiran_ke',
        'kondisi_ibu',
        'jk_bayi',
        'bb_bayi',
        'tb_bayi',
        'lingkar_kepala',
        'lingkar_dada',
        'frekuensi_napas',
        'detak_jantung',
        'kondisi_lahir',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Penunjang\Database\factories\PersalinanBayiFactory::new();
    }
}
