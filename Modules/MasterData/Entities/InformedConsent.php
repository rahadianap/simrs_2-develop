<?php

namespace Modules\MasterData\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformedConsent extends Model
{
    use HasFactory;

    protected $connection = 'dbclient';

    use SoftDeletes;
    public $incrementing = false;
    protected $table = 'tb_inform_template';
    protected $primaryKey = 'template_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
