<?php

namespace Modules\Pendaftaran\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemakaianBed extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_pemakaian_bed';
    public $incrementing = false;
    protected $primaryKey = 'pemakaian_bed_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Transaksi\Database\factories\PemakaianBedFactory::new();
    }
}
