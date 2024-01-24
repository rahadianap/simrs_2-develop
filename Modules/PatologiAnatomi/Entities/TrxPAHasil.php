<?php

namespace Modules\PatologiAnatomi\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrxPAHasil extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_trx_pa_hasil';
    protected $primaryKey =  ['detail_id','trx_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];

    protected $fillable = [
        'detail_id',
        'reg_id',
        'trx_id',
        'reff_trx_id',
        'tindakan_id',
        'tgl_hasil',
        'mikroskopis',
        'makroskopis',
        'kesimpulan',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
