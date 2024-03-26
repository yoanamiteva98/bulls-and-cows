<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Game;

class GameController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $topGames = Game::where('user_id', $userId)
                ->where('finished_game', true)
                ->orderBy('guess_count')
                ->take(3)
                ->get();


        $topUsers = User::whereHas('games', function ($query) {
            $query->where('finished_game', 1);
        })
        ->orderBy('average_result')
        ->orderBy('give_ups')
        ->orderByDesc('played_games')
        ->orderBy('name')
        ->take(10)
        ->get();

        return view('dashboard', compact('topGames', 'topUsers'));
    }

    public function startGame()
    {
        $randomNumber = $this->generateRandomNumber();
        $userId = Auth::id();
        $game = new Game();
        $game->user_id = $userId;
        $game->guess_numbers = '';
        $game->guess_count = 0;
        $game->correct_number = $randomNumber;
        $game->save();
        $gameStarted = true;
        return response()->json(['gameId' => $game->id, 'gameStarted' => $gameStarted]);
    }


    public function guess(Request $request)
    {
        $userGuess = $request->input('userGuess');
        $gameId = $request->input('gameId');
        
        $uniqueDigits = count(array_unique(str_split($userGuess))) === 4;
        if (!$uniqueDigits) {
            return response()->json(['message' => 'Your guess must contain only unique digits. E.g. 1234'], 422);
        }

        $game = Game::findOrFail($gameId);
    
        $result = $this->calculateBullsAndCows($userGuess, $game->correct_number);
        $bulls = $result['bulls'];
        $cows = $result['cows'];
    
        $game->guess_numbers .= ($game->guess_numbers ? ', ' : '') . $userGuess; 
        $game->guess_count++;
        $game->save();
    
        $gameFinished = false;
        if ($bulls === 4 && $cows === 0) {
            $game->finished_game = true;
            $game->save();
            $message = "Congratulations, you win! The number was indeed $game->correct_number";
            $gameFinished = true;
            $this->updateUserStatistics($game, 'win');
        } else {
            $message = "The number $userGuess has $bulls bulls and $cows cows.";
        }
    
        return response()->json(['message' => $message, 'gameFinished' => $gameFinished]);
    }

    private function generateRandomNumber()
    {
        $usedDigits = [];
        $randomNumber = '';
    
        for ($i = 0; $i < 4; $i++) {
            do {
                $digit = rand(0, 9); 
            } while (
                in_array($digit, $usedDigits) || 
                ($digit == 1 && !in_array(8, $usedDigits)) || 
                ($digit == 8 && !in_array(1, $usedDigits)) || 
                ($digit == 4 && $i % 2 == 0) || 
                ($digit == 5 && $i % 2 == 0) 
            );
    
            $randomNumber .= $digit;
            $usedDigits[] = $digit;
        }
    
        return $randomNumber;
    }

    public function calculateBullsAndCows($guess, $correctNumber)
    {
        $bulls = 0;
        $cows = 0;

        $correctDigits = str_split($correctNumber);
        $guessDigits = str_split($guess);
        foreach ($guessDigits as $index => $digit) {
            if (in_array($digit, $correctDigits)) {
                if ($correctDigits[$index] === $digit) {
                    $bulls++;
                } else {
                    $cows++;
                }
            }
        }

        return ['bulls' => $bulls, 'cows' => $cows];
    }

    public function giveUp(Request $request)
    {
        $gameId = $request->input('gameId');
        $game = Game::findOrFail($gameId);
        $correctNumber = $game->correct_number;
        $message = "Why did you give up? The correct number was $correctNumber. Want to play a new game?";
        $this->updateUserStatistics($game, 'give_up');
        return response()->json(['message' => $message, 'correctNumber' => $correctNumber]);
    }

    private function updateUserStatistics($game, $result)
    {
        $userId = $game->user_id;
        $user = User::findOrFail($userId);

        if ($result === 'win') {
            $games = Game::where('user_id', $userId)->pluck('guess_count');
            $averageGuessCount = $games->avg();
            $user->average_result = $averageGuessCount;
        } elseif ($result === 'give_up') {
            $user->give_ups++;
        }

        $user->played_games++;
        $user->save();
    }

}
