<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokVclaim extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_kelompok_vclaim';
    protected $primaryKey = 'kelompok_vclaim_id';
    public $incrementing = false;

    protected $fillable = [
        'kelompok_vclaim_id',
        'kelompok_vclaim',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\KelompokVclaimFactory::new();
    }
}
