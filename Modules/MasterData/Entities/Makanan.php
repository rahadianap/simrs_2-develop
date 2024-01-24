<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Makanan extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_makanan';

    protected $primaryKey = 'makanan_id';

    public $incrementing = false;

    protected $fillable = [
        'makanan_id',
        'nama_makanan',
        'kelompok',
        'jns_makanan',
        'catatan',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function meals_group() {
        return $this->hasOne(KelompokMakanan::class,'kelompok','kelompok');
    }

    public function menu(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'tb_menu_makanan', 'makanan_id', 'menu_id');
    }
}
