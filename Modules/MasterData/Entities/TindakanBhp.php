<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TindakanBhp extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_tindakan_bhp';
    protected $primaryKey = 'tindakan_bhp_id';
    public $incrementing = false;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $fillable = [];
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\TindakanBhpFactory::new();
    }
}
