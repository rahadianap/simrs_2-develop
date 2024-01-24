<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_menu';

    protected $primaryKey = 'menu_id';

    public $incrementing = false;

    protected $fillable = [
        'menu_id',
        'nama_menu',
        'is_menu_khusus',
        'catatan',
        'is_jual',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    // public function makanan(): BelongsToMany
    // {
    //     return $this->belongsToMany(Makanan::class, 'tb_menu_makanan', 'menu_id', 'makanan_id');
    // }
    public function makanan() {
        return $this->hasMany(Makanan::class,'makanan_id','makanan_id');
    }
}