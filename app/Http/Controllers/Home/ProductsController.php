<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Core\Setting;
use App\Models\Core\Categories;
use App\Models\Core\ProductsAttribute;
use App\Models\Web\Products;

class ProductsController extends Controller
{
	public function __construct(Setting $setting, Categories $categories, Products $products,ProductsAttribute $productsAttribute){
        $this->setting = $setting;
        $this->categories = $categories;
        $this->products = $products;
        $this->productsAttribute = $productsAttribute;
        $this->language_id =1;
    }

    public function index(Request $request){
        $title = array('pageTitle' => Lang::get('website.Product Detail'));
        $result = array();
 
        $products = $this->products->getProductsBySlug($request->slug);
        if(!empty($products) and count($products)>0){
            $category = $this->products->getCategoryByParent($products[0]->products_id);
        }else{
            $products = '';
            $result['detail']['product_data'] = '';
        }
        dd( $result);
        return view("web.detail", ['title' => $title)->with('result', $result);
     }
}
