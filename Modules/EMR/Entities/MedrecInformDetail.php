<?php

namespace Modules\EMR\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedrecInformDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_asesmen_detail';
    
    protected $primaryKey = 'asesmen_detail_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\EMR\Database\factories\MedrecInformDetailFactory::new();
    }
}
