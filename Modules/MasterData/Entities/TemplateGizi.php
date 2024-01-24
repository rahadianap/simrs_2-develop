<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateGizi extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $connection = 'dbclient';

    protected $table = 'tb_template_gizi';

    protected $primaryKey = 'temp_gizi_id';

    public $incrementing = false;

    protected $fillable = [
        'temp_gizi_id',
        'nama_template',
        'tgl_dibuat',
        'tgl_berlaku',
        'catatan',
        'jml_hari',
        'is_aktif',
        'client_id',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}
