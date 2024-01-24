<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitPelayanan extends Model
{
    //use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_unit';
    protected $primaryKey = 'unit_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
    public function otorisasi() {
        return $this->hasOne(UnitOtorisasi::class,'unit_id','unit_id');
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function rooms(){
        return $this->hasMany(RuangPelayanan::class,'unit_id','unit_id');
    }
}
