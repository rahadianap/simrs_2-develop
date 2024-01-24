<?php

namespace Modules\Persediaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustments extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_stock_adjustment';
    protected $primaryKey = 'sa_id';
    public $incrementing = false;

    protected $fillable = [
        'sa_id',
        'tgl_dibuat',
        'tgl_sa',
        'tgl_selesai',
        'catatan',        
        'depo_id',
        'depo_nama',
        'unit_id',
        'approver_id',
        'approver',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
