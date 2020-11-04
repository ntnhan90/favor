<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'code',
        'symbol_left', 
        'symbol_right',
        'decimal_point' , 
        'decimal_places', 
        'value',
        'is_default', 
        'status',
        'is_current', 
    ];
}
