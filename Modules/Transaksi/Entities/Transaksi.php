<?php

namespace Modules\Transaksi\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_transaksi';
    protected $primaryKey =  'trx_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $fillable = [
        'reg_id',
        'trx_id',
        'is_sub_trx',
        'jns_transaksi',
        'tgl_transaksi',
        'no_antrian',
        'no_transaksi',
        'kelas_id',
        'kelas_harga_id',
        'kelas_penjamin_id',
        'penjamin_id',
        'penjamin_nama',
        'unit_id',
        'unit_nama',
        'ruang_id',
        'ruang_nama',
        'dokter_id',
        'dokter_nama',
        'dokter_pengirim_id',
        'dokter_pengirim',
        'unit_pengirim_id',
        'unit_pengirim',
        'pasien_id',
        'no_rm',
        'nama_pasien',
        'status',
        'is_realisasi',
        'is_bayar',
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
