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
  		public function command($text, $user) {
   			$action = explode(" ", $text);
	   		$response = array();
	   		try {
	   			if (count($action) == 1) {
	   			   	# commands with no parameters
		   			switch ($action[0]) {
		   				case 'board':
		   					$response['attachments'] = array();
		   					array_push($response['attachments'], generateBoardImageData());
		   					break;
		   				case 'leaderboard':
		   					$leaderboard = $game->getLeaderboard();
		   					if (count($leaderboard) == 0) {
		   						$response['text'] = 'No data yet.';
		   					} else {
		   						$response['text'] = '';
		   						$rank = 1;
			   					foreach ($leaderboard as $player => $scores)
		    						$response['text'] += $player . ' is the ' . $rank . 'st player with ' . $scores['wins'] . ' and ' . $scores['losses'] . ', having a W\L ratio of ' . $scores['W\L'] . '!\n';
		    						$rank += 1;
		    						if ($rank == 5) {
		    							breakl;
		    						}
			   					}
		   					break;
		   				case 'help':
		   					# ui to functionality mapping stored in controller
		   					$response['text'] = configs['helpData'];
		   					break;
		   				case 'resign':
		   					$game->resign($user);
		   					$winner = $game->getWinner();
		   					$loser = $game->getLoser();
		   					$response['text'] = $loser . ' has resigned. ' . $winner . 'has won!'.
		   					break;
		   				case 'tie':
		   					$game->raiseTieFlag($user);
		   					if (!$game->getGameAlive()) {
		   						$response['text'] = 'Game tied!';
		   					} else {
		   						$response['text'] = $user . 'raised a tie flag!';
		   					}
		   					break;
		   				default:
		   					throw new SyntaxException($text);	
		   			}
	   			} else {
	   				# commands with parameters
		   			switch ($action[0]) {
		   				case 'play':
		   					if (count($action) != 3 or !is_int($action[2])) {
		   						throw new SyntaxException($text);	
		   					}
		   					$game->newGame($action[2], $user, $action[1]);
		   					response['text'] = 'Game started between ' . $user . ' (X) and ' . $action[1] . ' (O)!';
		   					break;
		   				case 'move':
		   				   	if (count($action) != 2 or !is_int($action[1])) {
		   						throw new SyntaxException($text);	
		   					}
		   					$game->makeMove($user, $action[1]);

		   					$response['text'] = $user . ' has made a play at position ' . $action[1] . '!';

		   					$response['attachments'] = array();
		   					array_push($response['attachments'], generateBoardImageData());
		   					break;
		   				default:
		   					throw new SyntaxException($text);	
		   			}
	   			} 
	   		} catch (Exception $e) {
	   			response['text'] = $e->__toString();
	   		}
	   		return $response;
   		}

   		# generate a board image
   		private function generateBoardImageData() {
			# generate board image URL
			$board = $game->getBoard();
			$boardLength = $game->getBoardLength();
			$winData = $game->getWinData();
			$squareLength = configs['squareLength'];
			$boardImage = new GameBoardImage($boardLength, $board, $winData, $squareLength);
			$boardImageURL = $_SERVER['SERVER_NAME'] . '/' . $boardImage->outputImage();


			$numTurn = $game->getNumberOfTurns();

			$boardImageData = array();
			$boardImageData['text'] = 'The board at turn ' . $numTurn . '.';
			$boardImageData['color'] = '#36a64f';
			$boardImageData['image_url'] = $boardImageURL;
		   	$boardImageData['footer'] = 'Tic-Tac-Toe';
		   	$boardImageData['fallback'] = 'An image of the game board';
		   	return $boardImageData;
   		}
	}

?>