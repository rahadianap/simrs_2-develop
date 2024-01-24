<?php

namespace Modules\Pendaftaran\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class RawatJalan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_rawat_jalan';
    public $incrementing = false;
    protected $primaryKey = 'trx_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Pendaftaran\Database\factories\RawatJalanFactory::new();
    }

    /**
     * Set the keys for a save update query.
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){ return parent::setKeysForSaveQuery($query); }
        foreach($keys as $keyName){ $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName)); }
        return $query;
    }

    /**
     * Get the primary key value for a save query.
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){ $keyName = $this->getKeyName(); }
        if (isset($this->original[$keyName])) { return $this->original[$keyName]; }
        return $this->getAttribute($keyName);
    }
}
