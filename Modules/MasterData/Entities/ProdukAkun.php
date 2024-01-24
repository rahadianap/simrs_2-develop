<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukAkun extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [];
    protected $connection = 'dbclient';
    protected $table = 'tb_produk_akun';
    protected $primaryKey = 'produk_akun_id';
    public $incrementing = false;
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\ProdukAkunFactory::new();
    }
}
