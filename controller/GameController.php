<?php
	require_once("../model/Game");

	# controller class for the Game
	class GameController {
		const private $configs = include('resources/config.php');
		
		# game model
		private $game;

		public function __construct() {
			# load previous state information
			$state = file_get_contents('../resources/' + configs['gameControllerSaveName']);
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
  			file_put_contents('../resources/' + configs['gameControllerSaveName'], $state);
   		}

   		# mapping from user input to game logic
  		public function command($text) {
   			$action = explode(" ", $text);

   			if (count($action) == 1) {
   			   	# commands with no parameters
	   			switch ($action[0]) {
	   				case 'board':
	   					break;
	   				case 'leaderboard':
	   					break;
	   				case 'help':
	   					break;
	   				class 'resign':
	   					break;
	   				default:
	   					throw new SyntaxError(configs['syntaxErrorMessage'], $text);	
	   			}
   			} else {
   				# commands with parameters
	   			switch ($action[0]) {
	   				case 'play':
	   					break;
	   				case 'move':
	   					break;
	   				default:
	   					throw new SyntaxError(configs['syntaxErrorMessage'], $text);	
	   			}
   			} 
   		}
	}

?>