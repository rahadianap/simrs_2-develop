<?php

namespace Modules\Persediaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class StockOpname extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_stock_opname';
    protected $primaryKey = 'so_id';
    public $incrementing = false;

    protected $fillable = [
        'so_id',
        'tgl_dibuat',
        'tgl_so',
        'tgl_selesai',
        'catatan',
        'depo_id',
        'depo_nama',
        'unit_id',
        'status',
        'approver_id',
        'approver',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
