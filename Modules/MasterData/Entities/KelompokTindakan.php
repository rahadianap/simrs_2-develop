<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokTindakan extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_kelompok_tindakan';

    protected $primaryKey = 'kelompok_tindakan_id';

    public $incrementing = false;

    protected $fillable = [
        'kelompok_tindakan_id',
        'kelompok_tindakan',
        'deskripsi',
        'billing_group_id',
        'billing_group',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
