<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'maint_ticket_id',
        'maint_schedule_id',
        'teknisi',
        'tgl_rencana',
        'tgl_realisasi',
        'catatan',
        'tindak_lanjut',
        'nama_vendor',
        'is_vendor',
        'is_aktif',
        'client_id',
        'created_by'
    ];
    
    protected $connection = 'dbclient';
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_maintenance_schedule';       
    
    protected $primaryKey = 'maint_schedule_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    protected static function newFactory()
    {
        return \Modules\Asset\Database\factories\MaintenanceScheduleFactory::new();
    }
}
