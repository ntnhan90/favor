<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use App\Models\Currency;

class CurrencyRepository extends BaseRepository implements CurrencyInterface
{

    public function getModel()
    {
        return \App\Models\Currency::class;
    }
}