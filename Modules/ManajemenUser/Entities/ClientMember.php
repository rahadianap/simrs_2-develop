<?php

namespace Modules\ManajemenUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class ClientMember extends Model
{
    // use HasFactory;

    // protected $fillable = [];
    
    // protected static function newFactory()
    // {
    //     return \Modules\ManajemenUser\Database\factories\ClientMemberFactory::new();
    // }
    protected $connection = 'dbcentral';
    
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_client_member';
    protected $primaryKey = ['user_id','client_id'];
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function detail() {
        return $this->hasOne(Client::class,'client_id','client_id');
    }

    public function user() {
        return $this->hasOne(User::class,'user_id','user_id');
    }
}
