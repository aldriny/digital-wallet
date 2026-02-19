<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawTransaction extends Model
{
    protected $fillable = [
        "payload",
    ];
}
