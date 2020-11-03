<?php
namespace App\Repositories;

interface MenuInterface
{
	public function getTree($parent = 0, &$tree = null, $menus = null, &$st = '');
}