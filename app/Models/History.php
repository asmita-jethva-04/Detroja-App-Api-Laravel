<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    //
    protected $fillable = [
        'name',
        'old_data',
        'new_data',
        'status',
        'is_delete',
    ];
}
