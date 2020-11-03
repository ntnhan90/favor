<?php
namespace App\Repositories;

interface CateInterface
{
	public function getTree($parent = 0, &$tree = null, $menus = null, &$st = '');

}