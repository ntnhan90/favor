<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model{
    public static function imageType(){
        $extensions = array('gif', 'jpg', 'jpeg', 'png');
        return $extensions;
    }

    public function getSettings(){
        $setting = DB::table('settings')->orderby('id', 'ASC')->get();
        return $setting;
    }
    
    public function fetchLanguages(){
        $language = DB::table('languages')->get();
        return $language;
    }

    public function slugify($slug)
    {

        $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);

        // transliterate
        if (function_exists('iconv')) {
            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        }

        // remove unwanted characters
        $slug = preg_replace('~[^-\w]+~', '', $slug);

        // trim
        $slug = trim($slug, '-');

        // remove duplicate -
        $slug = preg_replace('~-+~', '-', $slug);

        // lowercase
        $slug = strtolower($slug);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }
}
