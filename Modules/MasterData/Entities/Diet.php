<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diet extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_diet';

    protected $primaryKey = 'diet_id';

    public $incrementing = false;

    protected $fillable = [
        'diet_id',
        'nama_diet',
        'catatan',
        'komplikasi',
        'is_semua_kelas',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
