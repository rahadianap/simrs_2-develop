<?php

namespace Modules\ManajemenUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AppMenu;

class RoleMenu extends Model
{
    // use HasFactory;
    // protected $fillable = [];    
    // protected static function newFactory()
    // {
    //     return \Modules\ManajemenUser\Database\factories\RoleMenuFactory::new();
    // }
    protected $connection = 'dbclient';
    
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_role_menu';
    protected $primaryKey = ['role_id','client_id,menu_id'];
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
    
    public function detail() {
        return $this->hasOne(AppMenu::class,'menu_id','menu_id');
    }

    public function role()
    {
        //return $this->belongsTo('Brand');
        return $this->belongsTo(Role::class,'role_id','role_id');
    }
}
