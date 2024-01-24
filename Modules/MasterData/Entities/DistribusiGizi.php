<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistribusiGizi extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_dist_gizi';

    protected $primaryKey = 'dist_gizi_id';

    public $incrementing = false;

    protected $fillable = [
        'dist_gizi_id',
        'tgl_permintaan',
        'tgl_dibutuhkan',
        'unit_id',
        'catatan',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
