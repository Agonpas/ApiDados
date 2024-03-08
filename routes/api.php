<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
    //Route::apiResource('users', UserController::class);
    //Route::apiResource('games', GameController::class);
    Route::get('/users/{id}/games', [GameController::class, 'getGames']); //mostrar juegos
    Route::post('/users/{id}/games', [GameController::class, 'createGame']); //crear juegos
    Route::get('/users', [UserController::class, 'index']); //mostrar usuarios
    Route::get('/users/{id}/win-percentage', [GameController::class, 'winPercentage']); //porcentaje victorias
    Route::delete('/users/{id}/games', [GameController::class, 'destroyAllGames']); //eliminamos todos los juegos
});

