<?php

namespace Modules\Persediaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpnameDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_stock_opname_detail';
    protected $primaryKey = ['so_id','so_detail_id','depo_id','produk_id'];
    public $incrementing = false;
    
    protected $fillable = [
        'so_detail_id',
        'so_id',
        'depo_id',
        'produk_id',
        'depo_nama',
        'produk_nama',
        'total_awal',
        'total_so',
        'selisih',
        'status',
        'jenis_produk',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

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
