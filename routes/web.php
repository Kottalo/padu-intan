<?php

use Illuminate\Support\Facades\Route;

use App\Models\Customer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) return redirect('setting/project');

    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    $routes = App\Http\Controllers\RouteController::getRoutes();

    foreach ($routes as $route)
    {
        Route::prefix($route['prefix'])->group(function() use ($route) {
            foreach ($route['links'] as $link)
            Route::get($link['url'], function() use ($route, $link) {
                return view($route['prefix'] . '.' . $link['url'],
                [
                    'title' => $link['title'],
                    'user' => Auth::user(),
                    'customers' => Customer::get(),
                ]);
            });
        });
    }

    Route::resources([
        'projects' => 'ProjectController',
        'suppliers' => 'SupplierController',
        'customers' => 'CustomerController',
        'orders' => 'OrderController',
        'items' => 'ItemController',
        'order_items' => 'OrderItemController',
    ]);

    Route::post('/order_items/getItems', 'OrderItemController@getItems');
    Route::post('/suppliers/getSuppliersByProjectIds', 'SupplierController@getSuppliersByProjectIds');
});

Auth::routes();
