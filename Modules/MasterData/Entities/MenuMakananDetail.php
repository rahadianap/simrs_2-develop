<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuMakananDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_menu_makanan_detail';

    protected $primaryKey = 'menu_makanan_detail_id';

    public $incrementing = false;

    protected $fillable = [
        'menu_makanan_detail_id',
        'menu_makanan_id',
        'makanan_id',
        'nama_makanan',
        'is_standard',
        'is_optional',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
