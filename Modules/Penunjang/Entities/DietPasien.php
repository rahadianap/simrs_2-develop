<?php

namespace Modules\Penunjang\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Penunjang\Entities\DietPasienDetail;

class DietPasien extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    public $incrementing = false;
    protected $table = 'tb_pasien_diet';
    protected $primaryKey = 'pasien_diet_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $fillable = [
        'pasien_diet_id',
        'trx_id',
        'pasien_id',
        'nama_pasien',
        'unit_id',
        'nama_unit',
        'dokter_id',
        'nama_dokter',
        'start_date',
        'start_time',
        'catatan',
        'is_special',
        'diagnosa',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    public function diet_detail() {
        return $this->hasMany(DietPasienDetail::class,['pasien_diet_id','pasien_diet_id']);
    }
}
