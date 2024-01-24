<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bridging extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    protected $table = 'tb_bridging';
    protected $primaryKey = 'bridging_id';
    public $incrementing = false;

    protected $fillable = [
        // 'bridging_id',
        // 'bridging_group',
        // 'local_resource_id',
        // 'local_resource_name',
        // 'bridging_resource_id',
        // 'bridging_resource_name',
        // 'is_aktif',
        // 'client_id',
        // 'created_by',
        // 'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\BridgingFactory::new();
    }
}
