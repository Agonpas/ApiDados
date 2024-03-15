<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PassportController;

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


//Route::post('/register', [PassportController::class,'register']);
Route::post('/players', [PassportController::class,'register']);
Route::post('/login', [PassportController::class,'login']);
Route::post('/logout', [PassportController::class, 'logout']);

Route::post('/users/{id}/games', [GameController::class, 'createGame']); //crear juegos
Route::post('/players/{id}/games', [GameController::class, 'createGame']); //crear juegos

Route::delete('/users/{id}/games', [GameController::class, 'destroyAllGames']); //eliminamos todos los juegos
Route::delete('/players/{id}/games', [GameController::class, 'destroyAllGames']); //eliminamos todos los juegos

Route::get('/users/winPercentage', [GameController::class, 'allUsersWinPercentage']); //todos los user con sus porcentajes
Route::get('/players', [GameController::class, 'allUsersWinPercentage']); //todos los user con sus porcentajes

Route::get('/users/{id}/games', [GameController::class, 'getGames']); //mostrar juegos
Route::get('/players/{id}/games', [GameController::class, 'getGames']); //mostrar juegos

Route::put('/users/{id}', [UserController::class, 'updateName']); //modificar usuario
Route::put('/players/{id}', [UserController::class, 'updateName']); //modificar usuario

Route::get('/players/ranking', [GameController::class, 'ranking']); //Porcentaje total de victorias


Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
/*Route::middleware(middleware:'auth:api')->group(function () {
   
});*/
//players



Route::get('/users/{id}/winPercentage', [GameController::class, 'winPercentage']); //porcentaje victorias
//Admmin
 //mostrar usuarios

Route::get('/users/totalWinPercentage', [GameController::class, 'getTotalWinPercentage']); //Porcentaje total de victorias
Route::get('/players/ranking', [GameController::class, 'ranking']); //Porcentaje total de victorias

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
    Route::apiResource('users', UserController::class);
    Route::apiResource('games', GameController::class);
   
    
});

