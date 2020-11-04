<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Categories_description extends Model
{
    protected $table = 'categories_description';
    protected $primaryKey = 'id';
    protected $fillable = [
        'categories_id',
        'language_id', 
        'categories_name',
    ];
}
