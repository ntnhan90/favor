<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\Core\Customers;
use App\Models\Core\Images;
use App\Models\Core\Setting;
use App\Models\Core\Languages;
use Exception;
use Kyslik\ColumnSortable\Sortable;

class CustomersController extends Controller
{
    public function __construct(Customers $customers, Setting $setting)
    {
        $this->Customers = $customers;
        $this->myVarsetting = new SiteSettingController($setting);
        $this->Setting = $setting;
    }

    public function index(){
        $title = array('pageTitle' => Lang::get("labels.ListingCustomers"));
        $language_id = '1';
        $customers = $this->Customers->paginator();

        $result = array();
        $index = 0;
        foreach($customers as $customers_data){
            array_push($result, $customers_data);

            $devices = DB::table('devices')->where('user_id','=',$customers_data->id)->orderBy('created_at','DESC')->take(1)->get();
            $result[$index]->devices = $devices;
            $index++;
        }

        $customerData = array();

        $message = array();
        $errorMessage = array();

        $customerData['message'] = $message;
        $customerData['errorMessage'] = $errorMessage;
        $customerData['result'] = $customers;
        $result['commonContent'] = $this->Setting->commonContent();
      //  dd($result);
        return view("admin.customers.index", $title)->with('customers', $customerData)->with('result', $result);
    }

    public function add(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.AddCustomer"));
        $images = new Images;
        $allimage = $images->getimages();
        $language_id = '1';
        $customerData = array();
        $message = array();
        $errorMessage = array();
        $customerData['countries'] = $this->Customers->countries();
        $customerData['message'] = $message;
        $customerData['errorMessage'] = $errorMessage;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.customers.add", $title)->with('customers', $customerData)->with('allimage',$allimage)->with('result', $result);
    }


    //add addcustomers data and redirect to address
    public function insert(Request $request)
    {
        $language_id = '1';
        //get function from other controller
        $images = new Images;
        $allimage = $images->getimages();

        $customerData = array();
        $message = array();
        $errorMessage = array();

        //check email already exists
        $existEmail = $this->Customers->email($request);
        $this->validate($request, [
            'customers_firstname' => 'required',
            'customers_lastname' => 'required',
           
            'customers_telephone' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'isActive' => 'required',
        ]);


        if (count($existEmail)> 0 ) {
            $messages = Lang::get("labels.Email address already exist");
            return Redirect::back()->withErrors($messages)->withInput($request->all());
        } else {
            $customers_id = $this->Customers->insert($request);
            return redirect('admin/customers')->with(['success' => $message]);
        }
    }

    public function edit(Request $request){

      $images           = new Images;
      $allimage         = $images->getimages();
      $title            = array('pageTitle' => Lang::get("labels.EditCustomer"));
      $language_id      =   '1';
      $id               =   $request->id;

      $customerData = array();
      $message = array();
      $errorMessage = array();
      $customers = $this->Customers->edit($id);

      $customerData['message'] = $message;
      $customerData['errorMessage'] = $errorMessage;
      $customerData['countries'] = $this->Customers->countries();
      $customerData['customers'] = $customers;
      $result['commonContent'] = $this->Setting->commonContent();

      return view("admin.customers.edit",$title)->with('data', $customerData)->with('result', $result)->with('allimage', $allimage);
    }

    //add addcustomers data and redirect to address
    public function update(Request $request){
        $language_id  =   '1';
        $user_id				  =	$request->customers_id;

        $customerData = array();
        $message = array();
        $errorMessage = array();

        //get function from other controller
        if($request->image_id!==null){
            $customers_picture = $request->image_id;
        }	else{
            $customers_picture = $request->oldImage;
        }

        if($request->image_id){
            $uploadImage = $request->image_id;
            $uploadImage = DB::table('image_categories')->where('image_id',$uploadImage)->select('path')->first();
            $customers_picture = $uploadImage->path;
        }	else{
            $customers_picture = $request->oldImage;
        }

        $user_data = array(
            'gender'   		 	=>   $request->gender,
            'first_name'		=>   $request->first_name,
            'last_name'		 	=>   $request->last_name,
            'dob'	 			 	  =>	 $request->dob,
            'phone'	 	      =>	 $request->phone,
            'status'		    =>   $request->status,
            'avatar'	 		  =>	 $customers_picture,
            'updated_at'    => date('Y-m-d H:i:s'),
        );
        $customer_data = array(
          'customers_newsletter'   		 	=>   0,
          'updated_at'    => date('Y-m-d H:i:s'),
        );

        if($request->changePassword == 'yes'){
            $user_data['password'] = Hash::make($request->password);
        }

        $this->Customers->updaterecord($customer_data,$user_id,$user_data);
        return redirect('admin/customers');
    }

    public function delete(Request $request){
      	$this->Customers->destroyrecord($request->users_id);
      	return redirect()->back()->withErrors([Lang::get("labels.DeleteCustomerMessage")]);
    }

    public function filter(Request $request){
	    $filter    = $request->FilterBy;
	    $parameter = $request->parameter;

	    $title = array('pageTitle' => Lang::get("labels.ListingCustomers"));
	    $customers  = $this->Customers->filter($request);

	    $result = array();
	    $index = 0;
	    foreach($customers as $customers_data){
	        array_push($result, $customers_data);

	        $devices = DB::table('devices')->where('user_id','=',$customers_data->id)->orderBy('created_at','DESC')->take(1)->get();
	        $result[$index]->devices = $devices;
	        $index++;
	    }

      $customerData = array();
      $message = array();
      $errorMessage = array();

      $customerData['message'] = $message;
      $customerData['errorMessage'] = $errorMessage;
      $customerData['result'] = $customers;
      //dd($customers);
      $result['commonContent'] = $this->Setting->commonContent();

      return view("admin.customers.index",$title)->with('result', $result)->with('customers', $customerData)->with('filter',$filter)->with('parameter',$parameter);
    }
}
