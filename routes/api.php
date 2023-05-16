<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatoController;
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

//Route login
Route::post('/auth', [AuthController::class, 'auth']);

//Route create user
Route::post('/lead', [AuthController::class, 'lead']);

Route::middleware('auth:api')->group(function() {

    //candidato
    Route::controller(CandidatoController::class)
        ->group(function () {
        Route::post('/lead', 'create');
        Route::get('/lead/{id}', 'get');
        Route::get('/leads', 'all');
    });

});
