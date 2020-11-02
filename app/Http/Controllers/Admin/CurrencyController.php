<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Core\Languages;
use App\Models\Core\Currency;
use App\Models\Core\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Exception;
use App\Models\Core\Images;

class CurrencyController extends Controller
{
    public function __construct(Currency $currencies, Setting $setting)
  	{
	    $this->currencies = $currencies;
	    $this->Setting = $setting;
  	}

	public function index(){
	    $title = array('pageTitle' => Lang::get("labels.Currency"));
	    $currencies = $this->currencies->paginator();
	    $result['commonContent'] = $this->Setting->commonContent();
	    return view("admin.currencies.index",$title)->with('result', $result)->with('currencies', $currencies);
	}
}
