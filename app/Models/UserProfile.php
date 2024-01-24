<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    //use HasFactory;
    protected $connection = 'dbcentral';        
    use SoftDeletes;
    
    public $incrementing = false;
    protected $table = 'tb_user_profile';
    protected $primaryKey = "user_id";
    protected $dates = ['created_at','updated_at','deleted_at'];
}
