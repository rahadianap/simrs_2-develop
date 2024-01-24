<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitTindakan extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_unit_tindakan';

    protected $primaryKey = 'unit_tindakan_id';

    public $incrementing = false;

    protected $fillable = [
        'unit_tindakan_id',
        'unit_id',
        'tindakan_id',
        'is_tampil_dokter',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
