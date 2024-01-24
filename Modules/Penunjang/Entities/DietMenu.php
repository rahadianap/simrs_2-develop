<?php

namespace Modules\Penunjang\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DietMenu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_diet_menu';
    protected $primaryKey = 'diet_menu_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $fillable = [
        'diet_menu_id',
        'diet_id',
        'nama_diet',
        'menu_id',
        'nama_menu',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Penunjang\Database\factories\DietMenuFactory::new();
    }
}
