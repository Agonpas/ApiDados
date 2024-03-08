<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameCollection;
use Illuminate\Http\Request;
use App\Filters\GameFilter;
use App\Models\User;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new GameFilter();
        $queryItems = $filter->transform($request);
        if(count($queryItems) == 0) {
            return new GameCollection(Game::paginate());
        } else {
            $games = Game::where($queryItems)->paginate();
            return new GameCollection($games->appends($request->query()));
        }
        
    }
    public function getGames($userId)
    {
        // Obtén el usuario
        $user = User::findOrFail($userId);

        // Obtén todos los juegos asociados al usuario
        $games = $user->games;

        return response()->json($games, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createGame(Request $request, $userId)
    {
        
        $user = User::find($userId);
       if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $sum = $dice1 + $dice2;
        $won = $sum === 7 ? true : false;
        
        $game = Game::create([
            'user_id' => $user->id,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'won' => $won
        ]);
        return response()->json(['message' => 'Juego creado exitosamente', 'game' => $game], 201);
    }
    public function winPercentage($userId) 
    {
        $user = User::find($userId);
        if (!$user) {
            return  response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $totalGames = $user->games()->count();
        $totalWins = $user->games()->where('won', true)->count();
        if ($totalGames === 0) {
            $winPercentage = 0;
        } else {
            $winPercentage = ($totalWins / $totalGames) * 100;
        }
        return response()->json([
            'user_id' => $user->id,
            'win_percentage' => $winPercentage,
            'total_games' => $totalGames,
            'total_wins' => $totalWins

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }
}
