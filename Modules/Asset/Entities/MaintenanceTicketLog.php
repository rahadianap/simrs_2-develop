<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTicketLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'maint_ticket_log_id',
        'maint_ticket_id',
        'tgl_log',
        'keterangan',
        'status',
        'is_aktif',
        'client_id',
        'created_by'
    ];
    
    protected $connection = 'dbclient';
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_maintenance_ticket_log';
    
    protected $primaryKey = 'maint_ticket_log_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    protected static function newFactory()
    {
        return \Modules\Asset\Database\factories\MaintenanceTicketLogFactory::new();
    }
}
