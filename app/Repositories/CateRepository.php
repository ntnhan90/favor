<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use App\Models\Category;

class CateRepository extends BaseRepository implements CateInterface
{

    public function getModel()
    {
        return \App\Models\Category::class;
    }


    public function getTree($parent = 0, &$tree = null, $menus = null, &$st = '')
    {
        $where = array(  
            'type' => 1,
        );    
        $menus = $menus ?? self::findWhere($where)->groupBy('parent_id');

        $tree = $tree ?? [];
        $lisMenu = $menus[$parent] ?? [];
        foreach ($lisMenu as $menu) {
            $tree[$menu->id] = $st . ' ' . $menu->name;
            if (!empty($menus[$menu->id])) {
                $st .= '--';
                $this->getTree($menu->id, $tree, $menus, $st);
                $st = '';
            }
        }

        return $tree;
    }
}