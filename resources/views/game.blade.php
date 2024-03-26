<!-- Game Blade (game.blade.php) -->
<div>
    <div id="guessContainer">
    <h2 class="card-header mt-0">Guess the Number</h2>
    <p>Enter a number:</p>
    <form id="guessForm">
        <input type="number" class="guess-input" id="userGuess" name="userGuess" min="1000" max="9999" required>
        <button type="submit" class="btn btn-primary">Guess</button>
    </form>
    </div>
    <div class="d-flex">
        <div id="result"></div>
        <button id="giveUpButton" class="btn btn-primary gray" style="display: none;">Give Up</button> 
    </div>
</div>

<script>

</script>
