<?php

namespace Modules\PatologiAnatomi\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatologiTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_pa_template';
    protected $primaryKey =  'pa_template_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\PatologiAnatomi\Database\factories\PatologiTemplateFactory::new();
    }
}
