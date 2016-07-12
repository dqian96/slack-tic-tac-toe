<?php

return array(
    'syntaxExceptionMessage' => 'The inputted command is not syntatically valid.',
    
    'squareLength' => 100,

    'helpData' 

    => 'Welcome to Tic-Tac-Toe! This app is an nxn version of the popular game tic-tac-toe. Currently, a channel can only have one game played at a time. A game ends when a player has won, resigns, or both players declare a time. The follwing are a list of the game\'s commands: \n 
    	
    	"play <player user name who use wish to challenge> <board length>": This command instantiates a new game between you and the player who you wish to challenge, set on a given board length. Note that the board length must be between 3-49 inclusive, and be an odd number. The person who run this command plays with an "X" \n

    	"board": Any player can run this command to see the board of a current game. \n
    	
    	"resign": A player may resign the current game. \n
    	
    	"tie": If both players run this command, the current game is tied. \n
    	
    	"help": Displays this message. \n
    	
    	"leaderboard": May be run anytime to see the top 10 players. \n
    	
    	"move <position>": "The player whose turn it is may run this command to play a move on the board. The position is defined as the nth square counting each row left to right from the topmost row to the bottom most. The top-left square has position 0. \n'
);

?>
