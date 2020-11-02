<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\Currency;
use App\Models\Web\Index;
use App\Models\Web\Languages;
use App\Models\Web\News;
use App\Models\Web\Order;
use App\Models\Web\Products;
use Auth;
use Carbon;
use Illuminate\Support\Facades\Mail;
use Lang;
use View;
use DB;
use Cookie;

class IndexController extends Controller
{
	public function __construct( Index $index,News $news, Languages $languages,
        Products $products, Currency $currency,  Order $order
    ) {
        $this->index = $index;
        $this->order = $order;
        $this->news = $news;
        $this->languages = $languages;
        $this->products = $products;
        $this->currencies = $currency;
    }

    public function index(){
    	$title = array('pageTitle' => Lang::get("website.Home"));

    	return view('home.index');
    }

    public function maintance()
    {
        return view('errors.maintance');
    }
}
