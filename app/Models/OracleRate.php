<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleRate extends Model
{
    protected $fillable = [
        'rate_code',
        'name',
    ];
}
