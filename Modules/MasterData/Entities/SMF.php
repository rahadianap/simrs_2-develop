<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMF extends Model
{
    protected $fillable = ['smf_nama'];    
    protected $connection = 'dbclient';
    
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_smf';
    protected $primaryKey = 'smf_id';
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
