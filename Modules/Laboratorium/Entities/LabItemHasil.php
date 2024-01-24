<?php

namespace Modules\Laboratorium\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabItemHasil extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_lab_hasil';
    protected $primaryKey = 'lab_hasil_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    
    protected $fillable = ['hasil_id','hasil_nama','klasifikasi'];
    
    protected static function newFactory()
    {
        return \Modules\Laboratorium\Database\factories\LabHasilFactory::new();
    }

    public function normal() {
        return $this->hasMany(LabNormal::class,'lab_hasil_id','lab_hasil_id');
    }
}
