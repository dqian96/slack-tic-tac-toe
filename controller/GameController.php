<?php
	require_once("../model/Game.php");
	require_once("GameControllerExceptions.php");

	# controller class for the Game
	class GameController {
		const private $configs = include('resources/controller-config.php');
		
		# game model
		private $game;

		public function __construct() {
			# load previous state information
			$state = file_get_contents('../resources/' + configs['gameSaveName']);
			if (!$state) {
				# create a new game model if no state exists
				$game = new Game;
			} else {
				# load state from file
				$game = unserialize($state);
			}
   		}


		public function __destruct() {
			# save game state
			$state = serialize($game);
  			file_put_contents('../resources/' + configs['gameSaveName'], $state);
   		}

   		# mapping from user input to game logic
  		public function command($text) {
   			$action = explode(" ", $text);

   			if (count($action) == 1) {
   			   	# commands with no parameters
	   			switch ($action[0]) {
	   				case 'board':
	   					$board = $game->getBoard();
	   					break;
	   				case 'leaderboard':
	   					break;
	   				case 'help':
	   					break;
	   				case 'resign':
	   					break;
	   				case 'tie':
	   					break;
	   				default:
	   					throw new SyntaxException($text);	
	   			}
   			} else {
   				# commands with parameters
	   			switch ($action[0]) {
	   				case 'play':
	   					break;
	   				case 'move':
	   					break;
	   				default:
	   					throw new SyntaxException($text);	
	   			}
   			} 
   		}
	}

?>