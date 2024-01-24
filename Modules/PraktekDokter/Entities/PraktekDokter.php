<?php

namespace Modules\PraktekDokter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PraktekDokter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_praktek_dokter';
    protected $primaryKey = 'reg_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\PraktekDokter\Database\factories\PelayananFactory::new();
    }
}
