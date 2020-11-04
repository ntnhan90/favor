<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;

class Language extends Model
{
    protected $table = 'languages';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'code',
        'image', 
        'directory',
        'sort_order' , 
        'direction', 
     	'status',
        'is_default', 
    ];
}
