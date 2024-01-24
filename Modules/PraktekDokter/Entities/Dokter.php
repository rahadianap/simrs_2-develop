<?php

namespace Modules\PraktekDokter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokter extends Model
{
   use HasFactory;
   use SoftDeletes;

   protected $fillable = [];
   protected $connection = 'dbclient';
   public $incrementing = false;
   protected $table = 'tb_dokter';
   protected $primaryKey = 'dokter_id';
   protected $dates = ['created_at','updated_at','deleted_at'];
   protected $hidden = ['created_at','updated_at','deleted_at'];

}
