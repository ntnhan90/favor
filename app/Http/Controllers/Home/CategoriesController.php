<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Core\Setting;
use App\Models\Core\Categories;
//use App\Models\Core\Products;
use App\Models\Core\ProductsAttribute;
use App\Models\Web\Products;

class CategoriesController extends Controller{
	public function __construct(Setting $setting, Categories $categories, Products $products,ProductsAttribute $productsAttribute){
        $this->setting = $setting;
        $this->categories = $categories;
        $this->products = $products;
        $this->productsAttribute = $productsAttribute;
        $this->language_id =1;
    }

    public function index(Request $request){
        if (!empty($request->page)) {
            $page_number = $request->page;
        } else {
            $page_number = 0;
        }

        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 10;
        }

        if (!empty($request->type)) {
            $type = $request->type;
        } else {
            $type = '';
        }

        if (!empty($request->price)) {
            $d = explode(";", $request->price);
            $min_price = $d[0];
            $max_price = $d[1];
        } else {
            $min_price = '';
            $max_price = '';
        }

    	$title = array('pageTitle' => Lang::get('websi0te.Shop'));
    	$language_id =  1;
             
        $result['categories'] = $this->categories->allcategories($language_id);
        $result['attributes'] = $this->productsAttribute->display($request);

        $categories_slug = $request->slug;
        $data = array('page_number' => $page_number,'categories_slug' => $categories_slug,'min_price' => $min_price, 'max_price' => $max_price,'limit' => $limit,'type' => $type);
        $products = $this->products->getproductsInCate($data);   

        $result['count'] = count($products);
        $result['products'] = $products;
      
    	return view('home.cate.index')->with('result', $result);
    }
}
