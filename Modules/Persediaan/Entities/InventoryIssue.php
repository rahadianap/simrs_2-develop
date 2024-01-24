<?php

namespace Modules\Persediaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryIssue extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_inven_issue';
    protected $primaryKey = 'inven_issue_id';
    public $incrementing = false;

    protected $fillable = [
        'inven_issue_id',
        'tgl_dibuat',
        'tgl_issue',
        'tgl_selesai',
        'catatan',
        'depo_id',
        'depo_nama',
        'unit_id',
        'status',
        'approver_id',
        'approver',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
