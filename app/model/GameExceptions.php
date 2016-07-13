<?php
	# exception class for game board errors
	class BoardException extends Exception {
		private $boardLength;
		
		# mandatory message and (invalid) board length
	    public function __construct($boardLength, $code = 0, Exception $previous = null) {
	    	$configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/game-config.php');
	   		parent::__construct($configs['boardExceptionMessage'], $code, $previous);
	   		$this->boardLength = $boardLength;
	    }

	    # getter for boardLength
	    public function getBoardLength() {
	    	return $this->boardLength;
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $this->message . '\n' . 'The board length that was entered is: ' . $this->boardLength;
	    	return $error;
	    }
	}

	# exception class for when a player not in the game tries to make a move  
	class PlayerNotInGameException extends Exception {
		private $playerNotInGame;
		private $configs;

		# mandatory message and (invalid) player
	    public function __construct($playerNotInGame, $code = 0, Exception $previous = null) {
	    	$configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/game-config.php');
	   		parent::__construct($configs['playerNotInGameExceptionMessage'], $code, $previous);
	   		$this->playerNotInGame = $playerNotInGame;
	    }

	    # getter for playerNotInGame
	    public function getPlayerNotInGame() {
	    	return $this->playerNotInGame;
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $this->message . '\n' . 'The player who tried to make a move was: ' . $this->playerNotInGame;
	    	return $error;
	    }
	}

	# exception class for when a player tries to make a move, but does not have turn right
	class WrongTurnException extends Exception {
		private $playerWithTurnRight;
		private $wrongPlayer;

		# mandatory message and valid and invalid players
	    public function __construct($wrongPlayer, $playerWithTurnRight, $code = 0, Exception $previous = null) {
	    	$configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/game-config.php');
	   		parent::__construct($configs['wrongTurnExceptionMessage'], $code, $previous);
	   		$this->playerWithTurnRight = $playerWithTurnRight;
	   		$this->wrongPlayer = $wrongPlayer;
	    }

	    # getter for player with turn right
	    public function getPlayerWithTurnRight() {
	    	return $this->playerWithTurnRight;
	    }

	   	# getter for player who tried to make a move, wrongfully
	    public function getWrongPlayer() {
	    	return $this->wrongPlayer;
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $this->message . '\n' . 'It is not ' . $this->wrongPlayer . '\'s turn to play... ' . $this->playerWithTurnRight . 'must play.';
	    	return $error;
	    }
	}

	# exception class for when a game either exists or does not exist, but it is assumed otherwise
	class GameExistenceException extends Exception {
		# mandatory message determined by $falsePositive
		# $falsePositive == 1: assumed game exists, but it doesn't
		# $falsePositive == 0: assumed game doesn't exist, but it does (false negative)
	    public function __construct($falsePositive, $code = 0, Exception $previous = null) {
	    	$configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/game-config.php');
	    	if ($falsePositive) {
	    		$resultantMessage = $configs['gameDoesNotExistExceptionMessage'];
	    	} else {
	    		$resultantMessage = $configs['gameDoesExistExceptionMessage'];
	    	}
	   		parent::__construct($resultantMessage, $code, $previous);
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $this->message;
	    	return $error;
	    }
	}

	# exception class for when a wrong move is played
	# i.e. already played or out of range of board
	class WrongMoveException extends Exception {
		private $move;

		# getter for wrong move
	    public function getMove() {
	    	return $this->move;
	    }

	    public function __construct($move, $code = 0, Exception $previous = null) {
	    	$configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/game-config.php');
	    	$this->move = $move;
	   		parent::__construct($configs['wrongMoveExceptionMessage'], $code, $previous);
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $this->message . ' The move was: '. $move . '.';
	    	return $error;
	    }
	}

	# exception class for when an user attempts to fetch game data but no games were played
	class GameNeverPlayed extends Exception {
	    public function __construct($code = 0, Exception $previous = null) {
	    	$configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/game-config.php');
	   		parent::__construct($configs['gameNeverPlayedException'], $code, $previous);
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $this->message;
	    	return $error;
	    }
	}
?>