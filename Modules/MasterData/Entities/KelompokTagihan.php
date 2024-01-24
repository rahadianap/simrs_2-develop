<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokTagihan extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_kelompok_tagihan';
    protected $primaryKey = 'kelompok_tagihan_id';
    public $incrementing = false;

    protected $fillable = [
        'kelompok_tagihan_id',
        'kelompok_tagihan',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
