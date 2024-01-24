<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tb_template_master';
    protected $primaryKey = 'template_id';
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    protected $fillable = [];
    public $incrementing = false;
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\TemplateMasterFactory::new();
    }
}
