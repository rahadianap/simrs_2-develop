<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'maint_ticket_id',
        'asset_id',
        'jenis_maintenance',
        'tgl_tiket',
        'keterangan',
        'prioritas',
        'lokasi_asset',
        'nama_teknisi',
        'tindakan',
        'catatan_tindakan',
        'tgl_selesai',
        'status',
        'is_aktif',
        'client_id',
        'created_by'
    ];
    
    protected $connection = 'dbclient';
    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_maintenance_ticket';
    
    protected $primaryKey = 'maint_ticket_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    protected static function newFactory()
    {
        return \Modules\Asset\Database\factories\MaintenanceTicketFactory::new();
    }
}
