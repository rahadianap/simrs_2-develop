<?php

namespace Modules\Pengadaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_pr';
    protected $primaryKey = 'pr_id';
    public $incrementing = false;

    protected $fillable = [
        'pr_id',
        'tgl_pr',
        'tgl_kebutuhan',
        'catatan',
        'unit_id',
        'depo_id',
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
