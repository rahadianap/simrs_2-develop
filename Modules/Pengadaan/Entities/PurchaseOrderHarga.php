<?php

namespace Modules\Pengadaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderHarga extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_pengadaan_harga';
    // protected $primaryKey = 'detail_id';
    public $incrementing = false;

    protected $fillable = [
        'detail_id',
        'pengadaan_id',
        'trx_id',
        'reff_trx_id',
        'produk_id',
        'produk_nama',
        'satuan_id',
        'satuan_beli_id',
        'konversi',
        'jml_transaksi',
        'jml_total_transaksi',
        'harga',
        'tipe_pajak',
        'persen_pajak',
        'harga_sblm_pajak',
        'nilai_pajak',
        'is_diskon_persen',
        'diskon',
        'harga_akhir',
        'subtotal',
        'hna',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
