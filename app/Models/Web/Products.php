<?php

namespace App\Models\Web;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Session;

class Products extends Model
{
    public function getproductsInCate($data)
    {
        if (empty($data['page_number']) or $data['page_number'] == 0) {
            $skip = $data['page_number'] . '0';
        } else {
            $skip = $data['limit'] * $data['page_number'];
        }

        $min_price = $data['min_price'];
        $slug = $data['categories_slug'];
        $max_price = $data['max_price'];
        $take = $data['limit'];
        $currentDate = time();

        $products = DB::table('products')
            ->select('products.*', 
                'products_description.products_name as products_name', 
                'image_categories.path as path')
            ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id')
            ->leftJoin('products_to_categories', 'products_to_categories.products_id', '=', 'products.products_id')
            ->leftJoin('products_images', 'products_images.products_id', '=', 'products.products_id')
            ->leftJoin('image_categories', 'image_categories.image_id', '=', 'products_images.image')
            ->leftJoin('categories', 'categories.categories_id', '=', 'products_to_categories.categories_id')
            ->where( 'categories.categories_slug', '=', $slug )
           // ->distinct()
            ->groupBy('products.products_id')
            ->paginate($take);
       // $products = $products->unique('products_id');
        return $products;
    }

}
