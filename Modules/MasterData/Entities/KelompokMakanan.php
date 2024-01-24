<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokMakanan extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_kelompok_makanan';

    protected $primaryKey = 'kelompok_makanan_id';

    public $incrementing = false;

    protected $fillable = [
        'kelompok_makanan_id',
        'kelompok',
        'catatan',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
