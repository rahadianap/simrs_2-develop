<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $connection = 'dbclient';
    protected $table = 'tb_kelas';
    protected $primaryKey = 'kelas_id';

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
    

    protected $fillable = ['kelas_id','kelas_nama','is_aktif','client_id'];
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\KelasFactory::new();
    }
}
