<?php

namespace Modules\Persediaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distribusi extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_distribusi';
    protected $primaryKey = 'distribusi_id';
    public $incrementing = false;

    protected $fillable = [
        'distribusi_id',
        'unit_id',
        'tgl_dibuat',
        'tgl_dibutuhkan',
        'tgl_realisasi',
        'catatan',
        'depo_penerima_id',
        'depo_pengirim_id',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
