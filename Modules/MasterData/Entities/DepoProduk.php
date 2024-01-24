<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepoProduk extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_depo_produk';
    protected $primaryKey = 'depo_produk_id';
    public $incrementing = false;

    protected $fillable = [
        'depo_produk_id',
        'depo_id',
        'produk_id',
        'jml_awal',
        'jml_masuk',
        'jml_keluar',
        'jml_penyesuaian',
        'jml_total',
        'total_so',
        'selisih_si',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function produk() {
        return $this->hasOne(Produk::class,'produk_id','produk_id');
    }

    public function depo() {
        return $this->hasOne(Depo::class,'depo_id','depo_id');
    }
}
