<?php

namespace Modules\AntrianBpjs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Registrasi extends Model
{
    use HasFactory;
    protected $connection = 'dbclient';
    
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_registrasi';
    protected $primaryKey = 'reg_id';
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\AntrianBpjs\Database\factories\RegistrasiFactory::new();
    }
}
