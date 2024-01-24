<?php

namespace Modules\Penunjang\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DietPasienLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_pasien_diet_log';
    protected $primaryKey = 'pasien_diet_log_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $fillable = [
        'pasien_diet_log_id',
        'pasien_diet_id',
        'trx_id',
        'status',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
}
