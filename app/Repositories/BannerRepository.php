<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use App\Models\Banner;

class BannerRepository extends BaseRepository implements BannerInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Banner::class;
    }
}