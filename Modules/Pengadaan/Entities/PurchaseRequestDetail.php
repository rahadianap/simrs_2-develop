<?php

namespace Modules\Pengadaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_pr_detail';
    protected $primaryKey = 'pr_id';
    public $incrementing = false;

    protected $fillable = [
        'pr_id',
        'produk_id',
        'produk_nama',
        'satuan_id',
        'pr_satuan_id',
        'konversi',
        'jml_pr',
        'jml_satuan',
        'pengadaan_id',
        'trx_id',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function PurchaseRequest()
    {
        return $this->hasOne('Modules\Pengadaan\Entities\PurchaseRequest','pr_id','pr_id');
    }
}
