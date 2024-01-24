<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokter extends Model
{
    // use HasFactory;

    protected $fillable = [];
    
    // protected static function newFactory()
    // {
    //     return \Modules\MasterData\Database\factories\SpesialisasiFactory::new();
    // }
    protected $connection = 'dbclient';
    
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_dokter';
    protected $primaryKey = 'dokter_id';
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function smf() {
        return $this->hasOne(SMF::class,'smf_id','smf_id');
    }

    public function units() {
        return $this->hasMany(DokterUnit::class,'dokter_id','dokter_id');
    }
}
