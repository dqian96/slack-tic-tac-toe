<?php
	require_once("../model/Game.php");
	require_once("GameControllerExceptions.php");

	# controller class for the Game
	class GameController {
		const private $configs = include('../resources/controller-config.php');
		
		# game model
		private $game;

		public function __construct($game) {
			$this->game = $game;
   		}
   		
   		# mapping from user input to game logic
  		public function command($text) {
   			$action = explode(" ", $text);

   			if (count($action) == 1) {
   			   	# commands with no parameters
	   			switch ($action[0]) {
	   				case 'board':
	   					$board = $game->getBoard();
	   					$boardLength = $game->getBoardLength();
	   					$winData = $game->getWinData();
	   					$squareLength = configs['squareLength'];
	   					$boardImage = new GameBoardImage($boardLength, $board, $winData, $squareLength);

	   					$boardImageURL = $_SERVER['SERVER_NAME'] . '/' . $boardImage->outputImage();

	   					$numTurn = $game->getNumberOfTurns();

	   					$reply = array();
	   					$reply['text'] = "The board at turn " . $numTurn . " looks like:";
	   					$reply['attachments'] = array();
	   					array_push($reply['attachments'], )


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