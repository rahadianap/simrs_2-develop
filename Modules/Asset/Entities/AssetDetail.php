<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'detail_asset_id',
        'asset_id',
        'nama_asset',
        'nomor_seri',
        'lokasi',
        'deskripsi',
        'tgl_perolehan',
        'tgl_pemakaian',
        'nilai_asset',
        'masa_pakai',
        'status',
        'is_aktif',
        'client_id',
        'created_by'
    ];
    
    protected $connection = 'dbclient';
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_asset_detail';
    
    protected $primaryKey = 'asset_detail_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    protected static function newFactory()
    {
        return \Modules\Asset\Database\factories\AssetDetailFactory::new();
    }
}
