@extends('layouts.app')
@section('content')
<form method="POST" class="logout-form" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="logout-button">Logout</button>
</form>

<div class="container">
    <div class="col">
        <button id="playGameButton" class="btn btn-primary">Play Game</button>
        <div id="gameInterface" style="display: none;">
            @include('game') 
        </div>
    </div>
    <div class="col">
            @if ($topGames->isNotEmpty())
            <h2 class="mt-0 card-header">Your top 3 games</h2>
                <table class="small-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Guessed with</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($topGames as $key => $game)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $game->guess_count }} tries</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif

            @if ($topUsers->isNotEmpty())
            <h2 class="card-header">Top 10 Users</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>User</th>
                        <th>Result</th>
                        <th>Give Ups</th>
                        <th>Games</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topUsers as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ number_format($user->average_result, 2) }}</td>
                            <td>{{ $user->give_ups }}</td>
                            <td>{{ $user->played_games }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
    </div>
</div>

<script>
$(document).ready(function() {
    let gameId; 
    let gameFinished = false;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#playGameButton').click(function() {
        $('#result').html('');
        $('#gameInterface').show();
        $('#playGameButton').hide(); 
        $('#giveUpButton').show(); 
        $('#guessContainer').show();
        $.ajax({
            url: '/start-game',
            method: 'GET',
            success: function(response) {
                $('#gameArea').html(response);
                gameId = response.gameId;

                
            }
        });
    });

    $('#guessForm').submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: '/guess',
            method: 'POST',
            data: { gameId: gameId, userGuess: $('#userGuess').val() }, 
            success: function(response) {
                $('#result').append('<p>' + response.message + '</p>'); 
                $('#userGuess').val('');
                gameFinished = response.gameFinished;
                if(gameFinished){
                    $('#playGameButton').show(); 
                    $('#guessContainer').hide(); 
                    $('#giveUpButton').hide();
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    var response = JSON.parse(xhr.responseText);
                    $('#result').append('<p class="error">' + response.message + '</p>');
                } else {
                    $('#result').append('<p class="error"> Unexpected error</p>');
                }
            }
        });
    });
    $('#giveUpButton').click(function() {
            $.ajax({
                url: '/give-up',
                method: 'POST',
                data: { gameId: gameId },
                success: function(response) {
                    $('#result').append('<p>' + response.message + '</p>');
                    $('#playGameButton').show();
            $('#giveUpButton').hide();
            $('#guessContainer').hide();
            gameStarted = false;
                }
            });
        });
});

</script>
@endsection
