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


Route::post('/register', [PassportController::class,'register']);
Route::post('/login', [PassportController::class,'login'])->name('login');

/*Route::middleware(middleware:'auth:api')->group(function () {
   
});*/
//players
Route::get('/users/{id}/games', [GameController::class, 'getGames']); //mostrar juegos
Route::delete('/users/{id}/games', [GameController::class, 'destroyAllGames']); //eliminamos todos los juegos
Route::post('/users/{id}/games', [GameController::class, 'createGame']); //crear juegos
Route::get('/users/{id}/winPercentage', [GameController::class, 'winPercentage']); //porcentaje victorias
//Admmin
Route::get('/users', [UserController::class, 'index']); //mostrar usuarios
Route::get('/users/winPercentage', [GameController::class, 'allUsersWinPercentage']); //todos los user con sus porcentajes
Route::get('/users/totalWinPercentage', [GameController::class, 'getTotalWinPercentage']); //todos los user con sus porcentajes

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
    Route::apiResource('users', UserController::class);
    Route::apiResource('games', GameController::class);
   
    
});

