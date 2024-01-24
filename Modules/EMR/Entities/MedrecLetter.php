<?php

namespace Modules\EMR\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedrecLetter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'dbclient';
    protected $table = 'tb_medrec_letter';
    public $incrementing = false;
    protected $primaryKey = 'letter_id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['client_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];
    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\EMR\Database\factories\MedrecLetterFactory::new();
    }
}
