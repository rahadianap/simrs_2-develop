<?php

namespace Modules\HumanResource\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HRKeluarga extends Model
{
    use HasFactory;use SoftDeletes;
    protected $connection = 'dbclient';
    protected $table = 'tb_hr_keluarga';
    protected $primaryKey = 'hr_keluarga_id';
    public $incrementing = false;
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];


    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\HumanResource\Database\factories\HRKeluargaFactory::new();
    }
}
