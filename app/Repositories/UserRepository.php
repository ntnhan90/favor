<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;
use App\User;

class UserRepository extends BaseRepository implements UserInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\User::class;
    }
}