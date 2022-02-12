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
Route::group(['middleware' => 'api'], function ($router) {
    Route::post("/v1/signin", "App\Http\Controllers\AuthController@login"); 
    Route::post("/v1/signup", "App\Http\Controllers\AuthController@register"); 
    Route::put("/v1/changePassword", "App\Http\Controllers\AuthController@change_pass"); 
    Route::get("/v1/todos", "App\Http\Controllers\TodoController@index"); 
    Route::post("/v1/todos", "App\Http\Controllers\TodoController@store"); 
    Route::delete("/v1/todos/{id}", "App\Http\Controllers\TodoController@remove"); 
    Route::put("/v1/todos/{id}", "App\Http\Controllers\TodoController@update"); 
    Route::post("/v1/logout", "App\Http\Controllers\AuthController@logout");
});

