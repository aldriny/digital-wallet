<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'bank_id',
        "reference_number",
        "date",
        "amount",
        "status",
        "metadata"
    ];
}
