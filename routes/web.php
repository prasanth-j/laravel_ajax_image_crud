<?php

use App\Http\Controllers\ProductController;
use App\Models\Products;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

// This route should be added before the resource controller because it's a custom method.
Route::get('product/fetch', [ProductController::class, 'fetch'])->name('product.fetch');

Route::resource('product', ProductController::class);
