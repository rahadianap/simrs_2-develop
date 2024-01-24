<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dtd extends Model
{
    use HasFactory;

    protected $connection = 'dbclient';

    protected $fillable = [
        'no_dtd',
        'label_dtd',
        'nama_dtd',
        'is_aktif',
        'client_id',
        'created_by'
    ];

    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_dtd';
    protected $primaryKey = ['no_dtd','client_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
