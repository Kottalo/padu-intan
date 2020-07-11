<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouteController extends Controller
{
    public static function getRoutes()
    {
        $routes = [
          [
            'prefix' => 'setting', 'title' => '设定/增加', 'links' => [
              ['title' => '工程', 'url' => 'project',],
              ['title' => '材料供应商', 'url' => 'supplier',],
            ],
          ],
          [
            'prefix' => 'manage', 'title' => '工程管理', 'links' => [
              ['title' => '工程材料', 'url' => 'project_material',],
              ['title' => '供应商记录', 'url' => 'supplier_record',],
            ],
          ],
        ];

        return $routes;
    }
}
