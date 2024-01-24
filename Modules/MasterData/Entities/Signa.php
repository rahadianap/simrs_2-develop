<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Signa extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_signa';

    protected $primaryKey = 'signa_id';

    public $incrementing = false;

    protected $fillable = [
        'signa_id',
        'signa',
        'client_id',
        'is_aktif',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
