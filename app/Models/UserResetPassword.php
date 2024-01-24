<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserResetPassword extends Model
{
    //use HasFactory;
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_reset_password';
    protected $primaryKey = 'reset_id';
    protected $dates = ['created_at','updated_at','deleted_at'];
}
