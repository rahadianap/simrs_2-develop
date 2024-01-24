<?php

namespace Modules\Pengadaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_pengadaan';
    protected $primaryKey = 'pengadaan_id';
    public $incrementing = false;

    protected $fillable = [
        'pengadaan_id',
        'trx_id',
        'reff_trx_id',
        'jns_transaksi',
        'tgl_transaksi',
        'no_transaksi',
        'tgl_dibutuhkan',
        'vendor_id',
        'unit_id',
        'depo_id',
        'subtotal_harga',
        'total_pajak',
        'total_diskon',
        'total_harga',
        'catatan',
        'no_invoice',
        'tgl_invoice',
        'tgl_rencana_bayar',
        'data_versi',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by',
        'approved_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
