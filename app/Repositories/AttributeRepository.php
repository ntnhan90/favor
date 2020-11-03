<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use App\Models\Attribute;

class AttributeRepository extends BaseRepository implements AttributeInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Attribute::class;
    }
}