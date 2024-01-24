<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepoUnit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_depo_unit';
    protected $primaryKey = 'depo_unit_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'depo_unit_id',
        'depo_id',
        'unit_id',
    ];
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\DepoUnitFactory::new();
    }

}
