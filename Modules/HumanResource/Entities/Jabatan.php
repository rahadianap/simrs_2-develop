<?php

namespace Modules\HumanResource\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    protected $table = 'tb_jabatan';
    protected $primaryKey = 'jabatan_id';
    public $incrementing = false;
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];


    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\HumanResource\Database\factories\JabatanFactory::new();
    }
}
