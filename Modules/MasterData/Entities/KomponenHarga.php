<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomponenHarga extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_komp_harga';
    protected $primaryKey = 'komp_harga_id';
    public $incrementing = false;

    protected $fillable = [
        'komp_harga',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\KomponenHargaFactory::new();
    }
}
