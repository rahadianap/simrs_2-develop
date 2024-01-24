<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depo extends Model
{
    use HasFactory;

    protected $connection = 'dbclient';

    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_depo';
    protected $primaryKey = 'depo_id';

    protected $fillable = [
        'depo_id',
        'depo_nama',
        'deskripsi',
        'is_gudang',
        'is_opname',
        'is_virtual',
        'is_lock',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
