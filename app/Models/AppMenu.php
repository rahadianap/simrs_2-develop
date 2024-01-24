<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppMenu extends Model
{
    use HasFactory;

    protected $connection = 'dbcentral';        
    use SoftDeletes;
    
    public $incrementing = false;
    protected $table = 'tb_menu';
    protected $primaryKey = "menu_id";
    protected $dates = ['created_at','updated_at','deleted_at'];
}
