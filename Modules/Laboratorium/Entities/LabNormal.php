<?php

namespace Modules\Laboratorium\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabNormal extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_lab_normal';
    protected $primaryKey = ['lab_hasil_id','normal_group'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    
    protected $fillable = ['lab_hasil_id','normal_group','jenis_hasil'];

    protected static function newFactory()
    {
        return \Modules\Laboratorium\Database\factories\LabNormalFactory::new();
    }
}
