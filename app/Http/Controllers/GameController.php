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
    public function getGames(Request $request, $userId)
    {
        
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } elseif ($user->id != $userId) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
        }
        
        $games = $user->games;

        return response()->json($games, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createGame(Request $request, $userId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } elseif ($user->id != $userId) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
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
    private function calculateWinPercentage($userId)
{
    $totalGames = Game::where('user_id', $userId)->count();

    if ($totalGames === 0) {
        return 0;
    }

    $wonGames = Game::where('user_id', $userId)->where('won', true)->count();

    return ($wonGames / $totalGames) * 100;
}
    public function allUsersWinPercentage()
    {
        $users = User::all();
        $usersWithPercentage = [];
        foreach ($users as $user) {
            $winPercentage = $this->calculateWinPercentage($user->id);
            $usersWithPercentage[] = [
                'user' => $user,
                'win_percentage' => $winPercentage
            ];
        }
        return response()->json(['users' => $usersWithPercentage]);
    }
    public function ranking()
    {
        $users = User::all();
        $usersWithPercentage = [];
        foreach ($users as $user) {
            $winPercentage = $this->calculateWinPercentage($user->id);
            $usersWithPercentage[] = [
                'user' => $user,
                'win_percentage' => $winPercentage
            ];
        }
        $usersWithPercentageCollection = collect($usersWithPercentage);
        $usersWithPercentageSorted = $usersWithPercentageCollection->sortByDesc('win_percentage')->values()->all();
    
        return response()->json(['users' => $usersWithPercentageSorted]);
    }
    public function winner()
{
    $users = User::all();
    $maxWinPercentage = 0;
    $winners = [];

    foreach ($users as $user) {
        $winPercentage = $this->calculateWinPercentage($user->id);

        if ($winPercentage > $maxWinPercentage) {
            $maxWinPercentage = $winPercentage;
            $winners = [$user];
        } elseif ($winPercentage === $maxWinPercentage) {
            $winners[] = $user;
        }
    }

    return response()->json(['winners' => $winners, 'win_percentage' => $maxWinPercentage], 200);
}
public function loser()
{
    $users = User::all();
    $minWinPercentage = 100;
    $losers = [];

    foreach ($users as $user) {
        $winPercentage = $this->calculateWinPercentage($user->id);

        if ($winPercentage < $minWinPercentage) {
            $minWinPercentage = $winPercentage;
            $losers = [$user];
        } elseif ($winPercentage === $minWinPercentage) {
            $losers[] = $user;
        }
    }

    return response()->json(['losers' => $losers, 'win_percentage' => $minWinPercentage], 200);
}

    private function calculateTotalWinPercentage()
    {
        $totalGames = Game::count();

        if ($totalGames === 0) {
            return 0;
        }

        $wonGames = Game::where('won', true)->count();

        return ($wonGames / $totalGames) * 100;
    }
    public function getTotalWinPercentage()
    {
        
        $percentage = $this->calculateTotalWinPercentage();

        return response()->json(['win_percentage' => $percentage]);
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
    public function destroyAllGames(Request $request, $userId)
    {
        $user = $request->user();
        if  (!$user) {
            return  response()->json(['error' => 'Usuario no encontrado'], 404);
        } elseif ($user->id != $userId) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
        }
        $user->games()->delete();
        return response()->json(['message' => 'Todos los juegos del usuario han sido eliminados.']);
    }
}
