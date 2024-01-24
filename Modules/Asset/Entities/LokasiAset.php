<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LokasiAset extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_lokasi_aset';
    
    protected $primaryKey = 'lokasi_aset_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Asset\Database\factories\LokasiAsetFactory::new();
    }
}
