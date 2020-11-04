<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CateRepository;
use App\Models\Images;
use App\Models\Setting;
use Illuminate\Support\Facades\Lang;

class CategoryController extends Controller
{
    public function __construct(CateRepository $cateRepository, Setting $setting){
        $this->cateRepository = $cateRepository;
        $this->setting = $setting;
    }

    public function index(){
    	
   		$categories =  $this->cateRepository->with("categories_description")->with("images")->get(); 
   	//	dd($categories);
    	return view('admin.category.index' )->with('categories', $categories);
    }

    public function add(Request $request)
    {
        $images = new Images;
        $allimage = $images->getimages();

        $language_id = '1';
        $result['languages'] = $this->setting->fetchLanguages();

        $parents =  $this->cateRepository->getTree();

        return view("admin.category.add")->with('allimage',$allimage)->with('parents',$parents)->with('result',$result);
    }

    public function insert(Request $request){
    	$data = request()->all();
    	$data['slug'] = $this->setting->slugify($data['categoryName_1']);
    	//
    	$languages = $this->setting->fetchLanguages();

    	$categories_id = $this->cateRepository->create($data)->id;
    	
    	foreach($languages as $languages_data){
            $categoryName= 'categoryName_'.$languages_data->id;
           
            $categoryNameSub = $request->$categoryName;
            $languages_data_id = $languages_data->id;

            $subcatoger_des = $this->cateRepository->insertCateDes($categoryNameSub,$categories_id,$languages_data_id);
        }

        return redirect()->route('admin.categories');
    }

    public function edit(Request $request){
    	$images = new Images;
        $allimage = $images->getimages();

    	$id  =   $request->id;
    	$data =  $this->cateRepository->with("categories_description")->with("images")->find($id);
    	
    	$parents =  $this->cateRepository->getTree();

    	$language_id = '1';
        $result['languages'] = $this->setting->fetchLanguages();
        $result['data'] = $data;
        $result['categories_name'] = $data['categories_description']->categories_name;
        
        if(!is_null($data['images'])){
        	$result['oldImage_id'] = $data['images']->id;
        	$result['images'] = $data['images']->path;
        }
        else{
        	$result['images'] = $data['images'];
        }

    	return view("admin.category.edit")->with('allimage',$allimage)->with('result',$result)->with('parents',$parents);
    }

    public function update(Request $request){
	    $result = array();

	    $data = $request->all();
	    $data['slug'] = $this->setting->slugify($data['categoryName_1']);
	    $id = $request->id;
	   	
	    if($request->image!==null){
         	$data['image'] = $request->image;
    	}else{
         	$data['image'] = $request->oldImage;
     	}

	    $categories_id = $this->cateRepository->update($id,$data);
	    $languages = $this->setting->fetchLanguages();

	   	foreach($languages as $languages_data){
            $categoryName= 'categoryName_'.$languages_data->id;
           
            $categoryNameSub = $request->$categoryName;
            $languages_data_id = $languages_data->id;
            
       		$checkExist = $this->cateRepository->checkExit($id,$languages_data_id);
       		
         	if(count($checkExist)>0){
           		$category_des_update = $this->cateRepository->updateCateDes($categoryNameSub,$id,$languages_data_id);
       		}else{
           		$updat_des = $this->cateRepository->insertCateDes($categoryNameSub,$categories_id,$languages_data_id);
       		}
        }


        $message = Lang::get("labels.CategorieUpdateMessage");
     	return redirect('admin/categories')->withErrors([$message]);
    }
}
