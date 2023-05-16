<?php

use App\Http\Controllers\api\Apilogin;
use App\Http\Controllers\api\orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return 123;
    // return $request->user();
});
Route::controller(Apilogin::class)->group(function(){
    Route::post('/login','login');
});
Route::controller(orders::class)->group(function(){
    Route::post('orders/search','search');
    Route::post('orders/all','all');
    Route::post('orders/test','test');
    Route::post('orders/handle','handle');
    Route::post('orders/delivered','delivered');
    Route::post('orders/returned','returned');
    Route::post('orders/delayed','delayed');
    Route::post('orders/causes','causes');
    Route::post('orders/causes/delayed','causesdelayed');
    //status
    Route::post('orders/status/delivered','delevered_status');
    Route::post('orders/status/returned','returned_status');
    Route::post('orders/status/canceld','canceled_status');
    Route::post('orders/status/delay','delay_status');
    Route::post('orders/status/coordinate','coordinate_status');
    //store
    Route::post('orders/store/delivered','store_delivered');
    Route::post('orders/store/returned','store_returned');
    Route::post('orders/store/canceled','store_canceled');
    Route::post('orders/store/delayed','store_delayed');
    Route::post('orders/store/coordinated','store_coordinated');
    Route::post('orders/money','money');
});
