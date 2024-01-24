<?php

namespace Modules\Persediaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class KartuStock extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_kartu_stock';
    protected $primaryKey = 'stock_log_id';
    public $incrementing = false;

    protected $fillable = [
        'stock_log_id',
        'reg_id',
        'trx_id',
        'sub_trx_id',
        'produk_id',
        'detail_id',
        'tgl_log',
        'tgl_transaksi',
        'jns_transaksi',
        'aksi',
        'jns_produk',
        'produk_nama',
        'unit_id',
        'depo_id',
        'jml_awal',
        'jml_masuk',
        'jml_keluar',
        'jml_penyesuaian',
        'jml_akhir',
        'catatan',
        'keterangan',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
