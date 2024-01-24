<?php

namespace Modules\SatuSehat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BridgingLog extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_bridging_log';

    protected $primaryKey = 'bridging_log_id';

    public $incrementing = false;

    protected $fillable = [
        'bridging_log_id',
        'bridging_type',
        'bridging_resource',
        'bridging_phase',
        'state',
        'data',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
