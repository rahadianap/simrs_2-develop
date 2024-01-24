<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelurahan extends Model
{
    use HasFactory;

    protected $connection = 'dbclient';

    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_kelurahan';
    protected $primaryKey = 'kelurahan_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function propinsi() {
        return $this->hasOne(Propinsi::class,'propinsi_id','propinsi_id');
    }
    public function kota() {
        return $this->hasOne(Kota::class,'kota_id','kota_id');
    }
    public function kecamatan() {
        return $this->hasOne(Kecamatan::class,'kecamatan_id','kecamatan_id');
    }
}
