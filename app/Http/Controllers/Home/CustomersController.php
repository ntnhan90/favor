<?php

namespace App\Http\Controllers\Home;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Hash;
use Auth;
use Illuminate\Support\Facades\Lang;
use Validator;
use App\User;
use App\Models\Web\Shipping;

class CustomersController extends Controller
{
	public function __construct(User $user , Shipping $shipping) {
        $this->middleware('auth');
        $this->shipping = $shipping;
    }

    public function profile(User $user){
        $user = Auth()->user();
        return view('home.customer.profile', ['user' =>$user]);
    }

    public function updateProfile(Request $request){
    }

    public function changePassword(){
    }

    public function updateMyPassword(Request $request){
    }

    public function wishlist(){
    }

    public function likeProduct(Request $request){
    }

    public function unlikeProduct(){
    }

    public function orders(){
    }

    public function viewOrder(){
    }

    public function shippingAddress(Request $request){
        $result['address'] = $this->shipping->getShippingAddress($address_id='');
        //edit address
        if(!empty($request->address_id)){
            $result['editAddress'] = $this->shipping->getShippingAddress($request->address_id);
        }else{
            $result['editAddress'] = '';
        }

        $result['countries'] = $this->shipping->countries();
        return view('home.customer.address')->with('result', $result);
    }

    public function addAddress(Request $request){
        $this->shipping->addMyAddress($request);
        return redirect('/profile/shipping-address')->with('success', 'Your address has been added successfully!');
    }

    public function updateAddress(Request $request){
    }

    public function deleteAddress(Request $request){
    }
}
