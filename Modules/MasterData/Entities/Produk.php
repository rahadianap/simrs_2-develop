<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_produk';
    protected $primaryKey = 'produk_id';
    public $incrementing = false;

    protected $fillable = [
        'produk_id',
        'produk_nama',
        'klasifikasi',
        'satuan_id',
        'harga',
        'is_hasil_produksi',
        'is_jual',
        'is_pengadaan',
        'is_laundry_item',
        'vendor_id',
        'meta_data',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
