<?php

namespace Modules\ManajemenUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ManajemenUser\Entities\RoleMenu;

class Role extends Model
{
    use HasFactory;
    protected $connection = 'dbclient';
    use SoftDeletes;
    public $incrementing = false;
    
    // protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\ManajemenUser\Database\factories\RoleFactory::new();
    }
    protected $table = 'tb_role';
    protected $primaryKey = ['role_id','client_id'];
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function role_menus() {
        return $this->hasMany(RoleMenu::class,['role_id','role_id']);
    }
}
