<?php

namespace Modules\Pengadaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_pengadaan_detail';
    protected $primaryKey = 'detail_id';
    public $incrementing = false;

    protected $fillable = [
        'detail_id',
        'pengadaan_id',
        'reff_trx_id',
        'trx_id',
        'produk_id',
        'produk_nama',
        'depo_id',
        'satuan_id',
        'satuan_beli_id',
        'konversi',
        'jml_po',
        'jml_total_po',
        'jml_por',
        'jml_total_por',
        'jml_porr',
        'jml_total_porr',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function PurchaseOrder()
    {
        return $this->hasOne('Modules\Pengadaan\Entities\PurchaseOrder','pengadaan_id','pengadaan_id');
    }
}
