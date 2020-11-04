<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Image_category extends Model
{
    use Sortable;

    protected $table = 'image_categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'image_id',
        'image_type', 
        'height',
        'path' , 
    ];
}
