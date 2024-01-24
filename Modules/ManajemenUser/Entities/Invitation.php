<?php

namespace Modules\ManajemenUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitation extends Model
{
    // use HasFactory;

    // protected $fillable = [];
    
    // protected static function newFactory()
    // {
    //     return \Modules\ManajemenUser\Database\factories\InvitationFactory::new();
    // }
    protected $connection = 'dbcentral';
    
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_invitation';
    protected $primaryKey = 'invitation_id';
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
