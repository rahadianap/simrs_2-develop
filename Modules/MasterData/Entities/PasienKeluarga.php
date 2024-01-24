<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PasienKeluarga extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_pasien_keluarga';
    protected $primaryKey = ['pasien_id','client_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\MasterData\Database\factories\PasienKeluargaFactory::new();
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('client_id', $this->getAttribute('client_id'))
            ->where('pasien_id', $this->getAttribute('pasien_id'));
    }
}
