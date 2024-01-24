<?php

namespace Modules\Persediaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produksi extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_produksi';
    protected $primaryKey = 'produksi_id';
    public $incrementing = false;

    protected $fillable = [
        'produksi_id',
        'depo_id',
        'unit_id',
        'catatan',
        'tgl_produksi',
        'tgl_selesai',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
