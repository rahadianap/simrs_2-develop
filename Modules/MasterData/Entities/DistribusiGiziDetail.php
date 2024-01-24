<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistribusiGiziDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_dist_gizi_detail';

    protected $primaryKey = 'detail_id';

    public $incrementing = false;

    protected $fillable = [
        'dist_gizi_id',
        'detail_id',
        'tgl_permintaan',
        'tgl_dibutuhkan',
        'reg_id',
        'trx_id',
        'unit_id',
        'ruang_id',
        'diet_id',
        'kelas_id',
        'bed_id',
        'bed_no',
        'catatan',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by',
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
