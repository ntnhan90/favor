<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Categories_description;

class Category extends Model
{
	use Sortable;

	public function images(){
        return $this->belongsTo('App\Models\Image_category', 'image' ,'id');
    }

    public function icons(){
        return $this->belongsTo('App\Models\Image_category', 'icon' ,'id');
    }

    public function parent_name(){
        return $this->belongsTo('App\Models\Category', 'parent_id' ,'id');
    } 

    public function categories_description(){
        return $this->belongsTo('App\Models\Categories_description' ,'id', 'categories_id');
    }

    public $sortable =['id','created_at'];
    public $sortableAs =['categories_name'];

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'image',
        'icon', 
        'parent_id',
        'sort' , 
        'slug', 
        'status',
    ];
}
