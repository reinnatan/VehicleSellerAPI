<?php

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
    return $request->user();
});

//"VehicleController@createVehicle"
// [VehicleController::class, 'index']



Route::post('/login', 'VehicleController@login');
Route::post('/register', 'VehicleController@register');


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('/','VehicleController@index');
    Route::post('/add-vehicle', 'VehicleController@createVehicle');
    Route::post('/add-car', 'VehicleController@addCar');
    Route::post('/add-motorcycle', 'VehicleController@addMotorcycle');
    Route::get('/stock-vehicle', 'VehicleController@stockVehicle');
    Route::post('/sell-vehicle/{id}', 'VehicleController@sellVehicle');
    Route::get('/report-vehicles', 'VehicleController@reportSellerSpecVehicle');
    Route::get('/testing','VehicleController@testing');
});



