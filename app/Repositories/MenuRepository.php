<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use App\Models\Menu;

class MenuRepository extends BaseRepository implements MenuInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Menu::class;
    }

}