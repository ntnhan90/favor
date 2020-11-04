<?php
namespace App\Repositories;

interface CateInterface
{
	public function getTree($parent = 0, &$tree = null, $menus = null, &$st = '');

	public function insertCateDes($categoryNameSub,$categories_id,$languages_data_id);
}