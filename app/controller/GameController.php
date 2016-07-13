<?php
	require_once(__DIR__ . '/../model/Game.php');
	require_once(__DIR__ . '/../model/GameBoardImage.php');
	require_once('GameControllerExceptions.php');

	# controller class for the Game
	class GameController {
		private $configs;
		
		# game model
		private $game;

		public function __construct($game) {
			$this->configs = include(__DIR__ . '/../resources/controller-config.php');
			$this->game = $game;
   		}

   		# mapping from user input to game logic
  		public function command($text, $username) {
   			$action = explode(" ", $text);
	   		$response = array();
	   		try {
	   			if (count($action) == 0) {
   					throw new SyntaxException($text);	
   				}
	   			else if (count($action) == 1) {
	   			   	# commands with no parameters
		   			switch ($action[0]) {
		   				# command to fetch game board
		   				case 'board':
		   					$response['response_type'] = 'ephemeral';
		   					$boardImageData = $this->getBoardImageData();
		   					$response['attachments'] = array();
		   					array_push($response['attachments'], $boardImageData);
		   					break;
		   				# command to fetch leaderboard
		   				case 'leaderboard':
		   					$response['response_type'] = 'ephemeral';
		   					$leaderboard = $this->game->getLeaderboard();
		   					if (count($leaderboard) == 0) {
		   						$response['text'] = 'No data yet!';
		   					} else {
		   						$response['text'] = '';
		   						$rank = 1;
			   					foreach ($leaderboard as $player => $score) {
		    						$response['text'] .= $player . ' is the number ' . $rank . ' player with ' . $score['wins'] . ' win(s) and ' . $score['losses'] . ' loss(es), and having a W\L ratio of ' . $score['W\L'] . "!\n";
		    						$rank += 1;
		    						if ($rank == 5) {
		    							break;
		    						}
			   					}
			   				}
		   					break;
		   				# command to fetch help menu
		   				case 'help':
		   				   	$response['response_type'] = 'ephemeral';
		   					# ui to functionality mapping stored in controller
		   					$response['text'] = $this->configs['helpData'];
		   					break;
		   				# command to resign a game
		   				case 'resign':
		  		   			$response['response_type'] = 'in_channel';
		   					$this->game->resign($username);
		   					$winner = $this->game->getWinner();
		   					$loser = $this->game->getLoser();
		   					$response['text'] = $loser . ' has resigned. ' . $winner . ' has won!';
		   					break;
		   				# command to raise a tie flag
		   				case 'tie':
		   		  		   	$response['response_type'] = 'in_channel';
		   					$this->game->raiseTieFlag($username);
		   					if (!$this->game->getGameAlive()) {
		   						$response['text'] = 'Game tied!';
		   					} else {
		   						$response['text'] = $username . ' raised a tie flag!';
		   					}
		   					break;
		   				default:
		   					throw new SyntaxException($text);	
		   			}
	   			} else {
	   				# commands with parameters
		   			switch ($action[0]) {
		   				# command to start a new game
		   				case 'play':
		  		   		  	$response['response_type'] = 'in_channel';
		   					if (count($action) != 3 || strval($action[2]) != strval(intval($action[2]))) {
		   						# throw syntax error if wrong number of parameters or incorrect parameter types (i.e. not an integer)
		   						throw new SyntaxException($text);	
		   					}
		   					$this->game->newGame($action[2], $username, $action[1]);
		   					$response['text'] = 'Game started between ' . $username . ' (X) and ' . $action[1] . ' (O)! ' . $username . ' goes first!' ;
		   					$boardImageData = $this->getBoardImageData();
		   					$response['attachments'] = array();
		   					array_push($response['attachments'], $boardImageData);
		   					break;
		   				# command to play a move
		   				case 'move':
		   		   		  	$response['response_type'] = 'in_channel';
		   				   	if (count($action) != 2 || strval($action[1]) != strval(intval($action[1]))) {
		   						throw new SyntaxException($text);	
		   					}
		   					$this->game->makeMove($username, $action[1]);

		   					$response['text'] = $username . ' has made a play at position ' . $action[1] . '!';

		   					if (!$this->game->getGameAlive()) {
		   						if ($this->game->wasTied()) {
		   							$response['text'] .= "\nThis resulted in a tie game!";
		   						} else {
		   							$response['text'] .= "\nThis resulted in " . $this->game->getWinner() . ' winning the game!';
		   						}
		   					}
		   					$boardImageData = $this->getBoardImageData();
		   					$response['attachments'] = array();
		   					array_push($response['attachments'], $boardImageData);
		   					break;
		   				default:
		   					throw new SyntaxException($text);	
		   			}
	   			} 
	   		} catch (Exception $e) {
	   			$response['response_type'] = 'ephemeral';
	   			$response['text'] = $e->__toString();
	   		}
	   		return $response;
   		}

   		# helper function -- get board image data
   		private function getBoardImageData() {
			# get board image URL
			$board = $this->game->getBoard();
			$boardLength = $this->game->getBoardLength();
			$boardWinPatterns = $this->game->getBoardWinPatterns();
			$boardImage = new GameBoardImage($boardLength, $board, $boardWinPatterns);
			$boardImageURL = $boardImage->outputImage();

			$numTurn = $this->game->getNumberOfTurns();

			$boardImageData = array();
			$boardImageData['text'] = 'The board at turn ' . $numTurn . '.';
			$boardImageData['color'] = '#36a64f';
			$boardImageData['image_url'] = $boardImageURL;
		   	$boardImageData['footer'] = 'Tic-Tac-Toe';
		   	$boardImageData['fallback'] = 'An image of the game board.';
		   	return $boardImageData;
   		}
	}
?>