<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CurrencyRepository;

class CurrencyController extends Controller
{
	public function __construct(CurrencyRepository $currencyRepository){
        $this->currencyRepository = $currencyRepository;
    }

    public function index(){
    	$currencies =  $this->currencyRepository->paginate();
    	return view('admin.currency.index' )->with('currencies', $currencies);
    }
}
