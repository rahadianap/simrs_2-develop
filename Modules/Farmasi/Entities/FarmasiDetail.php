<?php

namespace Modules\Farmasi\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FarmasiDetail extends Model
{    
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_resep_detail';
    public $incrementing = false;
    // protected $primaryKey = 'detail_id,reg_id,trx_id,reff_trx_id';
    protected $primaryKey = 'detail_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [];
    protected static function newFactory()
    {
        return \Modules\Farmasi\Database\factories\FarmasiDetailFactory::new();
    }
}
