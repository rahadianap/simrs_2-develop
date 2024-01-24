<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'nomor_asset',
        'group_asset',
        'tgl_perolehan',
        'nama_asset',
        'brand',
        'is_aktif',
        'client_id',
        'created_by'
    ];
    
    protected $connection = 'dbclient';
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_asset';
    
    protected $primaryKey = 'asset_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    protected static function newFactory()
    {
        return \Modules\Asset\Database\factories\AssetFactory::new();
    }
}
