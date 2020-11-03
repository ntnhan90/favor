<?php
namespace App\Repositories;

interface StoreInterface
{
	public function getTree($parent = 0, &$tree = null, $menus = null, &$st = '');
}