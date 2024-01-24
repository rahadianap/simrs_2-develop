<?php

namespace Modules\Radiologi\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RadiologiTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_rad_template';
    protected $primaryKey =  'rad_template_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Radiologi\Database\factories\RadiologiTemplateFactory::new();
    }
}
