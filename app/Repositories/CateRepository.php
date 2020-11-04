<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use App\Models\Category;
use App\Models\Categories_description;
use Illuminate\Support\Facades\DB;

class CateRepository extends BaseRepository implements CateInterface
{

    public function getModel(){
        return \App\Models\Category::class;
    }
    
    public function getTree($parent = 0, &$tree = null, $menus = null, &$st = ''){
        $conditions = '=' ;
        $menus = $menus ?? $this->with("categories_description")->get()->groupBy('parent_id');
        $tree = $tree ?? [];
        $lisMenu = $menus[$parent] ?? [];
        foreach ($lisMenu as $menu) {
            $tree[$menu->id] = $st . ' ' . $menu->categories_description->categories_name;
            if (!empty($menus[$menu->id])) {
                $st .= '--';
                $this->getTree($menu->id, $tree, $menus, $st);
                $st = '';
            }
        }

        return $tree;
    }

    // insert into table categories and categories_description
    public function insertCateDes($categoryNameSub,$categories_id,$languages_data_id){
        DB::table('categories_description')->insert([
            'categories_name'   =>   $categoryNameSub,
            'categories_id'     =>   $categories_id,
            'language_id'       =>   $languages_data_id
        ]);
    }

    public function updateCateDes($categoryNameSub,$categories_id,$languages_data_id){
       $category =  DB::table('categories_description')->where('categories_id','=',$categories_id)
            ->where('language_id','=',$languages_data_id)->update([
                'categories_name'  =>  $categoryNameSub,
        ]);
       return $category;
    }

    public function checkExit($categories_id,$languages_data_id){
       
        $checkExist = DB::table('categories_description')->where('categories_id','=',$categories_id)->where('language_id','=',$languages_data_id)->get();
        return $checkExist;
    }
}