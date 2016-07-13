<?php
    return array(
        'syntaxExceptionMessage' => 'The inputted command is not syntatically valid.',
        
        'helpData'  

        => 'Welcome to Slick-Slack-Slow! ;) This app is an nxn version of the popular game tic-tac-toe. Currently, a channel can only have one game played at a time. A game ends when a player has won, resigns, or both players declare a tie. The follwing are a list of game commands:
        	
        	"play <player username who you wish to challenge> <board length>": This command starts a new game between you and the player who you wish to challenge, set on a given board length. Note that the board length must be between 3-49 inclusive, and be an odd number. The person who runs this command plays with an "X".

        	"board": Anybody can run this command to see the board of a current game.
        	
        	"resign": A player resigns the current game.
        	
        	"tie": If both players run this command, the current game is tied.
        	
        	"help": Displays the help menu.
        	
        	"leaderboard": Anybody may run this at any time to see the top 5 players.
        	
        	"move <position>": "The player whose turn it is may run this command to play a move on the board. The position is defined as the nth square counting each row left to right from the topmost row to the bottom most. The top-left square has position 0.'
    );
?>
