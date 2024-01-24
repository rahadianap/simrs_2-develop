<?php

namespace Modules\BridgingBpjs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BpjsSep extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\BridgingBpjs\Database\factories\BpjsSepFactory::new();
    }
}
