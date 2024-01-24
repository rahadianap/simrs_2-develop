<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReg extends Model
{
    protected $connection = 'dbcentral';        
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_user_reg';
    protected $primaryKey = "reg_id";
    protected $dates = ['created_at','updated_at','deleted_at'];
}
