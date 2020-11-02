<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Core\Languages;
use App\Models\Core\Setting;
use App\Models\Admin\Admin;
use App\Models\Core\Order;
use App\Models\Core\Customers;
use Exception;
use App\Models\Core\Images;
use Validator;
use Hash;
use Auth;
use ZipArchive;
use File;

class AdminController extends Controller{
	public function __construct(Admin $admin, Setting $setting, Order $order, Customers $customers)
    {
        $this->Setting = $setting;
        $this->Admin = $admin;
        $this->Order = $order;
		$this->Customers = $customers;
    }
    
    public function login(){
		if (Auth::check()) {
		  	return redirect('/admin/dashboard');
		}else{
			$title = array('pageTitle' => Lang::get("labels.login_page_name"));
			return view("admin.login",$title);
		}
	}

	public function admininfo(){
		$administor = administrators::all();
		return view("admin.login",$title);
	}

   	public function checkLogin(Request $request){
		$validator = Validator::make(
			array(
					'email'    => $request->email,
					'password' => $request->password
				),
			array(
					'email'    => 'required | email',
					'password' => 'required',
				)
		);
		if($validator->fails()){
			return redirect('admin/login')->withErrors($validator)->withInput();
		}else{
			$adminInfo = array("email" => $request->email, "password" => $request->password);

			if(auth()->attempt($adminInfo)) {
				$admin = auth()->user();

				$administrators = DB::table('users')->where('id', $admin->myid)->get();
				$categories_id = '';
				if(auth()->user()->adminType != '1'){
					$categories_role = DB::table('categories_role')->where('admin_id', auth()->user()->myid)->get();
					if(!empty($categories_role) and count($categories_role)>0){
						$categories_id = $categories_role[0]->categories_ids;
					}else{
						$categories_id = '';
					}
				}

				session(['categories_id' => $categories_id]);
				return redirect()->intended('admin/dashboard')->with('administrators', $administrators);
			}else{
				return redirect('admin/login')->with('loginError',Lang::get("labels.EmailPasswordIncorrectText"));
			}
		}
	}

	public function logout(){
		Auth::guard('admin')->logout();
		return redirect()->intended('admin/login');
	}

	public function adminProfile(Request $request){
		$title = array('pageTitle' => Lang::get("labels.Profile"));

		$result = array();

		$countries = DB::table('countries')->get();
		$zones = DB::table('zones')->where('zone_country_id', '=', auth()->user()->country)->get();

		$result['countries'] = $countries;
		$result['zones'] = $zones;
		$result['commonContent'] = $this->Setting->commonContent();

		return view("admin.profile",$title)->with('result', $result);
	}

	public function updateProfile(Request $request){
		$updated_at	= date('y-m-d h:i:s');
		$myVar = new SiteSettingController();
		$languages = $myVar->getLanguages();
		$extensions = $myVar->imageType();

		$uploadImage = $request->oldImage;
		$orders_status = DB::table('users')->where('id','=', Auth()->user()->id)->update([
				'user_name'		=>	$request->user_name,
				'first_name'	=>	$request->first_name,
				'last_name'		=>	$request->last_name,
				'country'		=>	$request->country,
				'phone'			=>	$request->phone,
				'avatar'		=>	$uploadImage,
				'updated_at'	=>	$updated_at
				]);

		$message = Lang::get("labels.ProfileUpdateMessage");
		return redirect()->back()->withErrors([$message]);
	}

	public function updateAdminPassword(Request $request){
		$orders_status = DB::table('users')->where('id','=', auth()->user()->myid)->update([
				'password'		=>	Hash::make($request->password)
				]);

		$message = Lang::get("labels.PasswordUpdateMessage");
		return redirect()->back()->withErrors([$message]);
	}

	public function profile(Request $request){

        $title = array('pageTitle' => Lang::get("labels.Profile"));
        $result = array();
        $images = new Images;
        $allimage = $images->getimages();
        $result['admin'] = $this->Admin->edit(auth()->user()->id);
        $countries = DB::table('countries')->get();
        $zones = DB::table('zones')->where('zone_country_id', '=', $result['admin']->entry_country_id)->get();
        $result['countries'] = $countries;
        $result['zones'] = $zones;
		$result['commonContent'] = $this->Setting->commonContent();
        return view("admin.admin.profile",$title)->with('result', $result)->with('allimage', $allimage);
    }

    public function update(Request $request){
     	$validator = Validator::make(
	        array(
	          	'first_name' => $request->first_name,
	          	'last_name' => $request->last_name,
	          	'address' => $request->address,
	          	'phone' => $request->phone,
	          	'city' => $request->city,
	         ),
	        array(
	         	'first_name' => 'required',
	          	'last_name' => 'required',
	          	'address' => 'required',
	          	'phone' => 'required',
	          	'city' => 'required',
	        )
	    );
	    if($validator->fails()){
	        return redirect()->back()->withErrors($validator)->withInput();
	    }

       	$update = $this->Admin->updaterecord($request);
        $message = Lang::get("labels.ProfileUpdateMessage");
       	return redirect()->back()->with(['success' => $message]);
    }

    public function updatepassword(Request $request){
        $update = $this->Admin->updatepassword($request);
        $message = Lang::get("labels.PasswordUpdateMessage");
        return redirect()->back()->withErrors([$message]);
    }

    public function deleteadmin(Request $request){
    	$myid = $request->myid;
    	DB::table('users')->where('id','=', $myid)->delete();
    	return redirect()->back()->withErrors([Lang::get("labels.DeleteAdminMessage")]);
    }

	public function admins(Request $request){
		$title = array('pageTitle' => Lang::get("labels.ListingCustomers"));
		$language_id            				=   '1';

		$result = array();
		$message = array();
		$errorMessage = array();

		$admins = DB::table('users')
			->leftJoin('user_types','user_types.user_types_id','=','users.role_id')
			->select('users.*','user_types.*')
			->where('users.role_id','>','10')
			->paginate(50);


		$result['message'] = $message;
		$result['errorMessage'] = $errorMessage;
		$result['admins'] = $admins;
		$result['commonContent'] = $this->Setting->commonContent();
		return view("admin.admins.index",$title)->with('result', $result);
	}

	public function addadmins(Request $request){
		$title = array('pageTitle' => Lang::get("labels.addadmin"));

		$result = array();
		$message = array();
		$errorMessage = array();

		//get function from ManufacturerController controller
		$myVar = new AddressController();
		$result['countries'] = $myVar->getAllCountries();

		$adminTypes = DB::table('user_types')->where('isActive', 1)->where('user_types_id','>','10')->get();
		$result['adminTypes'] = $adminTypes;
		$result['commonContent'] = $this->Setting->commonContent();

		return view("admin.admins.add",$title)->with('result', $result);
	}

	public function addnewadmin(Request $request){

		$myVar = new SiteSettingController();
		$extensions = $myVar->imageType();

		$result = array();
		$message = array();
		$errorMessage = array();

		$existEmail = DB::table('users')->where('email', '=', $request->email)->get();
		if(count($existEmail)>0){
			$errorMessage = Lang::get("labels.Email address already exist");
			return redirect()->back()->with('errorMessage', $errorMessage);
		}else{

			$uploadImage = '';

			$customers_id = DB::table('users')->insertGetId([
						'user_name'		 		    =>   $request->first_name.'_'.$request->last_name,
						'first_name'		 		=>   $request->first_name,
						'last_name'			 		=>   $request->last_name,
						'phone'	 					=>	 $request->phone,
						'email'	 					=>   $request->email,
						'password'		 			=>   Hash::make($request->password),
						'status'		 	 		=>   $request->isActive,
						'avatar'	 				=>	 $uploadImage,
						'role_id'					=>	 $request->adminType
						]);

			$message = Lang::get("labels.New admin has been added successfully");
			return redirect()->back()->with('message', $message);

		}
	}

	public function editadmin(Request $request){

		$title = array('pageTitle' => Lang::get("labels.EditAdmin"));
		$myid        	 =   $request->id;

		$result = array();
		$message = array();
		$errorMessage = array();

		//get function from other controller
		$myVar = new AddressController();
		$result['countries'] = $myVar->getAllCountries();

		$adminTypes = DB::table('user_types')->where('isActive', 1)->where('user_types_id','>','10')->get();

		$result['adminTypes'] = $adminTypes;

		$result['myid'] = $myid;

		$admins = DB::table('users')->where('id','=', $myid)->get();
		$zones = 0;

		if($zones>0){
			$result['zones'] = $zones;
		}else{
			$zones = new \stdClass;
			$zones->zone_id = "others";
			$zones->zone_name = "Others";
			$result['zones'][0] = $zones;
		}

		$result['admins'] = $admins;
		$result['commonContent'] = $this->Setting->commonContent();

		return view("admin.admins.edit",$title)->with('result', $result);
	}

	public function updateadmin(Request $request){

		$myVar = new SiteSettingController();
		$extensions = $myVar->imageType();
		$myid = $request->myid;
		$result = array();
		$message = array();
		$errorMessage = array();

		//check email already exists
		$existEmail = DB::table('users')->where([['email','=',$request->email],['id','!=',$myid]])->get();
		if(count($existEmail)>0){
			$errorMessage = Lang::get("labels.Email address already exist");
			return redirect()->back()->with('errorMessage', $errorMessage);
		}else{
			$uploadImage = '';

			$admin_data = array(
				'first_name'		 		=>   $request->first_name,
				'last_name'			 		=>   $request->last_name,
				'phone'	 					=>	 $request->phone,
				'email'	 					=>   $request->email,
				'status'		 	 		=>   $request->isActive,
				'avatar'	 				=>	 $uploadImage,
				'role_id'	 				=>	 $request->adminType,
			);

			if($request->changePassword == 'yes'){
				$admin_data['password'] = Hash::make($request->password);
			}
		//	dd($admin_data);
			$customers_id = DB::table('users')->where('id', '=', $myid)->update($admin_data);


			$message = Lang::get("labels.Admin has been updated successfully");
			return redirect('admin/admins')->with('message', $message);
		}
	}

	public function manageroles(Request $request){
		$title = array('pageTitle' => Lang::get("labels.manageroles"));
		$language_id            				=   '1';

		$result = array();
		$message = array();
		$errorMessage = array();

		$adminTypes = DB::table('user_types')->where('user_types_id','>','10')->paginate(50);

		$result['message'] = $message;
		$result['errorMessage'] = $errorMessage;
		$result['adminTypes'] = $adminTypes;
		$result['commonContent'] = $this->Setting->commonContent();

		return view("admin.admins.roles.manageroles",$title)->with('result', $result);
	}

	public function addadmintype(Request $request){
		$title = array('pageTitle' => Lang::get("labels.addadmintype"));

		$result = array();
		$message = array();
		$errorMessage = array();

		//get function from ManufacturerController controller
		$myVar = new AddressController();
		$result['countries'] = $myVar->getAllCountries();

		$adminTypes = DB::table('user_types')->where('isActive', 1)->get();
		$result['adminTypes'] = $adminTypes;
		$result['commonContent'] = $this->Setting->commonContent();

		return view("admin.admins.roles.addadmintype",$title)->with('result', $result);
	}

	public function addnewtype(Request $request){
		$result = array();
		$message = array();
		$errorMessage = array();

		$customers_id = DB::table('user_types')->insertGetId([
						'user_types_name'	 		=>   $request->user_types_name,
						'created_at'			 	=>   time(),
						'isActive'		 	 		=>   $request->isActive,
						]);

		$message = Lang::get("labels.Admin type has been added successfully");
		return redirect()->back()->with('message', $message);
	}

	public function editadmintype(Request $request){
		$title = array('pageTitle' => Lang::get("labels.EditAdminType"));
		$user_types_id        	 =   $request->id;

		$result = array();

		$result['user_types_id'] = $user_types_id;

		$user_types = DB::table('user_types')->where('user_types_id','=', $user_types_id)->get();

		$result['user_types'] = $user_types;
		$result['commonContent'] = $this->Setting->commonContent();
		return view("admin.admins.roles.editadmintype",$title)->with('result', $result);
	}

	public function updatetype(Request $request){
		$result = array();
		$message = array();
		$errorMessage = array();

		$customers_id = DB::table('user_types')->where('user_types_id',$request->user_types_id)->update([
						'user_types_name'	 		=>   $request->user_types_name,
						'updated_at'			 	=>   time(),
						'isActive'		 	 		=>   $request->isActive,
						]);


		$message = Lang::get("labels.Admin type has been updated successfully");
		return redirect()->back()->with('message', $message);
	}


	public function deleteadmintype(Request $request){

		$user_types_id = $request->user_types_id;

		DB::table('user_types')->where('user_types_id','=', $user_types_id)->delete();

		return redirect()->back()->withErrors([Lang::get("labels.DeleteAdminTypeMessage")]);

	}

}
