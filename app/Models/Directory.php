<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $fillable = [
        'name',
        'relations',
        'age',
        'surname',
        'qualification',
        'business',
        'marital_status',
        'home_country',
        'village',
        'current_address',
        'bussiness_address',
        'user_id',
        'child_id',
        'status',
        'is_delete',
        'village_id',
    ];
}
