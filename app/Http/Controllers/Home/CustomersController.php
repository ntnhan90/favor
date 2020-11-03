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
        $customers_id                           =   auth()->user()->id;
        $address_book_id                        =   $request->address_book_id;
        $entry_firstname                        =   $request->entry_firstname;
        $entry_lastname                         =   $request->entry_lastname;
        $entry_street_address                   =   $request->entry_street_address;
        $entry_suburb                           =   $request->entry_suburb;
        $entry_postcode                         =   $request->entry_postcode;
        $entry_city                             =   $request->entry_city;
        $entry_state                            =   $request->entry_state;
        $entry_country_id                       =   $request->entry_country_id;
        $entry_zone_id                          =   $request->entry_zone_id;
        $entry_gender                           =   $request->entry_gender;
        $entry_company                          =   $request->entry_company;
        $customers_default_address_id           =   $request->customers_default_address_id;

        if(!empty($customers_id)){
            $address_book_data = array(
                'entry_firstname'               =>   $entry_firstname,
                'entry_lastname'                =>   $entry_lastname,
                'entry_street_address'          =>   $entry_street_address,
                'entry_suburb'                  =>   $entry_suburb,
                'entry_postcode'                =>   $entry_postcode,
                'entry_city'                    =>   $entry_city,
                'entry_state'                   =>   $entry_state,
                'entry_country_id'              =>   $entry_country_id,
                'entry_zone_id'                 =>   $entry_zone_id,
                'customers_id'                  =>   $customers_id,
                'entry_gender'                  =>   $entry_gender,
                'entry_company'                 =>   $entry_company
            );

            $this->shipping->updateAddressBook($address_book_data,$address_book_id);

            if($customers_default_address_id == '1'){
             $this->shipping->updateCustomer($customers_id,$address_book_id);
            }
            return redirect('/profile/shipping-address?action=update');
        }
    }

    public function deleteAddress(Request $request){
    }
}
