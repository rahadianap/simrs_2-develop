<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuMakanan extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_menu_makanan';

    protected $primaryKey = 'menu_makanan_id';

    public $incrementing = false;

    protected $fillable = [
        'menu_makanan_id',
        'menu_id',
        'nama_menu',
        'jumlah',
        'satuan',
        'seq_no',
        'meal_set',
        'kelas',
        'deskripsi',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
