<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PasienAlergi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_pasien_alergi';
    protected $primaryKey = 'pasien_alergi_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\PasienAlergiFactory::new();
    }
}
