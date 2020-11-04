<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Users;
use App\Models\Images;
use App\Models\Setting;
use App\Models\Languages;
use Exception;
use Kyslik\ColumnSortable\Sortable;

class CustomerController extends Controller
{
    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    public function index(){
    	$users =  $this->userRepository->paginate();
   
    	return view('admin.customer.index' )->with('users', $users);
    }

    public function add(Request $request){
        $images = new Images;
        $allimage = $images->getimages();
        $language_id = '1';

        $customerData = array();
        $message = array();
        $errorMessage = array();

        $customerData['message'] = $message;
        $customerData['errorMessage'] = $errorMessage;
        return view("admin.customer.add")->with('customers', $customerData)->with('allimage',$allimage);
    }

    public function insert(Request $request){
    	$data = request()->all();
    	$validator = Validator::make($data, [
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string'],
            'phone' => ['required', 'string',  'max:15'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data['role'] = 4;
        $data['status'] = isset($request->status) ? 1 : 0 ;
        //dd($data);
    	$result = $this->userRepository->create($data);
        return redirect()->route('admin.customers');
    }

    public function edit(Request $request){
		$images           = new Images;
      	$allimage         = $images->getimages();
      	$language_id      =   '1';
      	$id               =   $request->id;
      	$data['users']  =  $this->userRepository->find($id);
      	return view("admin.customer.edit")->with('data', $data);
    }

    public function update(Request $request){
    	$data = request()->all();
    	$user_id = request()->user_id;
    	$customerData = array();
    	$validator = Validator::make($data, [
            'fullname' => ['required', 'string', 'max:255'],
           //'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
           // 'password' => ['required', 'string'],
            'phone' => ['required', 'string',  'max:15'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->userRepository->update($user_id,$data);
        return redirect('admin/customers');
    }

    public function delete(Request $request){
    	$user_id = $request->users_id;
      	$result = $this->userRepository->delete($user_id);
      	return redirect()->back()->withErrors([Lang::get("labels.DeleteCustomerMessage")]);
    }

    public function filter(Request $request){
	    $filter    = $request->FilterBy;
	    $parameter = $request->parameter;
	    $parameter = '%' .  $parameter . '%' ;
	    $where = array(  
            $filter =>  $parameter ,
        );
	  	$conditions = 'LIKE' ;
	   	$users = $this->userRepository->findWhere($where,$conditions);
	    
      	return view("admin.customer.index")->with('users', $users)->with('filter',$filter)->with('parameter',$parameter);
    }
}
