<?php

namespace Modules\PraktekDokter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemeriksaanDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_pemeriksaan_detail';
    protected $primaryKey = 'detail_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\PraktekDokter\Database\factories\PemeriksaanDetailFactory::new();
    }
}
