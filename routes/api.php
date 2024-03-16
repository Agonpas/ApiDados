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
//Rutas accesibles sin registro:
Route::post('/players', [PassportController::class,'register']); //registrarse
Route::post('/login', [PassportController::class,'login']); //iniciar sesión

//Rutas accesibles con registro
Route::middleware('auth:api')->group(function () {
    Route::put('/players/{id}', [UserController::class, 'updateName']); //modificar usuario
    Route::post('/logout', [PassportController::class, 'logout']); //cerrar sesión 
});

// Rutas para jugadores
Route::middleware(['auth:api', 'role:player'])->group(function () { 
    Route::post('/players/{id}/games', [GameController::class, 'createGame']); //crear juego
    Route::delete('/players/{id}/games', [GameController::class, 'destroyAllGames']); //eliminamos todos los juegos
    Route::get('/players/{id}/games', [GameController::class, 'getGames']); //mostrar juegos
});
// Rutas para administradores
Route::middleware(['auth:api', 'role:admin'])->group(function () {   
    Route::get('/players', [GameController::class, 'allUsersWinPercentage']); //todos los user con sus porcentajes
    Route::get('/players/ranking', [GameController::class, 'ranking']); //Ranking todos los usuarios
    Route::get('/players/ranking/winner', [GameController::class, 'winner']); //Ganadores
    Route::get('/players/ranking/loser', [GameController::class, 'loser']); //perdedores
});



//Route::post('/register', [PassportController::class,'register']);
//Route::post('/users/{id}/games', [GameController::class, 'createGame']); //crear juegos
//Route::post('/players/{id}/games', [GameController::class, 'createGame']); //crear juegos
//Route::delete('/users/{id}/games', [GameController::class, 'destroyAllGames']); //eliminamos todos los juegos
//Route::get('/users/winPercentage', [GameController::class, 'allUsersWinPercentage']); //todos los user con sus porcentajes
//Route::get('/users/{id}/games', [GameController::class, 'getGames']); //mostrar juegos
//Route::put('/users/{id}', [UserController::class, 'updateName']); //modificar usuario
//Route::get('/users', [UserController::class, 'index']);//lista de usuarios
//Route::get('/users/{id}/winPercentage', [GameController::class, 'winPercentage']); //porcentaje victorias
//Route::get('/users/totalWinPercentage', [GameController::class, 'getTotalWinPercentage']); //Porcentaje total de victorias


//modo dios
//Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
    //Route::apiResource('users', UserController::class);
    //Route::apiResource('games', GameController::class);   
//});

