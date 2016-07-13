<?php 
	require_once("GameExceptions.php");

	class Game {
		# bool indicating if there's an active game
		private $gameAlive; 

		# bool indicating that no game was ever played 
		private $neverPlayed;

		# players in current game
		private $player1;
		private $player2;
		private $currentPlayer;
		
		# if both players raise a tie flag, then the game ends in a tie (discarded) 
		private $player1TieFlag;
		private $player2TieFlag;

		# winner, loser, tie of prev game
		private $winner;
		private $loser;
		private $tie;

		# boardWinPatterns is an array containing the winning rows, columns, diagonals
		private $boardWinPatterns;

		# array of 0,-1,1 modelling the game board
		# player 1 (X): 1
		# player 2 (O): -1
		# unplayed: 0
		private $board;
		# width of board (the board is always a square with odd length)
		private $boardLength;

		private $numberOfTurns;

		# 2D array storing past player scores in reverse sorted order of W/L ratio
		private $leaderboard;
		
		public function __construct() {
			# member variable initialization

 			$this->leaderboard = array();

 			$this->neverPlayed = true;
			$this->gameAlive = false;

			$this->player1 = '';
			$this->player2 = '';
			$this->player1TieFlag = false;
			$this->player2TieFlag = false;

			$this->currentPlayer = '';
		
			$this->board = array();
	
			$this->winner = '';
			$this->loser = '';
			$this->tie = false;

			$this->boardWinPatterns = array(
				'row' => -1,
				'column' => -1,
				'diagonalLR' => -1,
				'diagonalRL' => -1,
			);
			$this->numberOfTurns = 0;
 		}

 		# getter functions
		public function wasTied() {
			if ($this->neverPlayed) {
				throw new GameNeverPlayed;
			}
			else if ($this->gameAlive) {
				throw new GameExistenceException(0);
			}
			return $this->tie;
		}
		
		public function getBoardWinPatterns() {
			if ($this->neverPlayed) {
				throw new GameNeverPlayed;
			}
			return $this->boardWinPatterns;
		}

		public function getWinner() {
			if ($this->neverPlayed) {
				throw new GameNeverPlayed;
			}
			else if ($this->gameAlive) {
				throw new GameExistenceException(0);
			}
			return $this->winner;
		}

		public function getLoser() {
			if ($this->neverPlayed) {
				throw new GameNeverPlayed;
			}
			else if ($this->gameAlive) {
				throw new GameExistenceException(0);
			}
			return $this->loser;
		}

		public function getBoardLength() {
			if ($this->neverPlayed) {
				throw new GameNeverPlayed;
			}
			return $this->boardLength;
		}

		public function getGameAlive() {
			return $this->gameAlive;
		}

		public function getBoard() {
			if ($this->neverPlayed) {
				throw new GameNeverPlayed;
			}
			# can still view board if game is over
			return $this->board;
		}

		public function getLeaderboard() {
			return $this->leaderboard;
		}


		public function getCurrentPlayer() {
			if (!$this->gameAlive) {
				throw new GameExistenceException(1);
			}
			return $this->currentPlayer;
		}


		public function getNumberOfTurns() {
			if ($this->neverPlayed) {
				throw new GameNeverPlayed;
			}
			return $this->numberOfTurns;
		}

 		# create a new game
		public function newGame($boardLength, $player1, $player2) {
			# throws an exception if a game already exists
			if ($this->gameAlive) {
				throw new GameExistenceException(0);
			} else if ($player1 == $player2) {
				throw new SamePlayerException;
			}

			# throws an exception if the board specifications are invalid
			if (!($boardLength >= 3 && $boardLength <= 49 && $boardLength % 2 == 1 )) {
				throw new BoardException($boardLength);
			}

			$this->neverPlayed = false;
			# set up players
			$this->player1 = $player1;
			$this->player2 = $player2;
			$this->player1TieFlag = false;
			$this->player2TieFlag = false;

			$this->currentPlayer = $player1;
			
			# refresh board, with dimensions, boardLength x boardLength
			$this->boardLength = $boardLength;
			$this->board = array();
			for ($i = 0; $i < $boardLength * $boardLength; $i++) {
				array_push($this->board, 0);
			}

			$this->tie = false;
			$this->winner = '';
			$this->loser = '';
			$this->boardWinPatterns = array(
				'row' => -1,
				'column' => -1,
				'diagonalLR' => -1,
				'diagonalRL' => -1,
			);

			$this->gameAlive = true;
			$this->numberOfTurns = 0;
		}

		# player makes a move on the board
		public function makeMove($player, $move) {
			# throws an exception if a game doesn't exists
			if (!$this->gameAlive) {
				throw new GameExistenceException(1);
			} 

			if ($player != $this->currentPlayer) {
				if ($player != $this->player1 && $player != $this->player2) {
					throw new PlayerNotInGameException($player);
				} else {
					throw new WrongTurnException($player, $this->currentPlayer);
				}
			} else {
				if ($move < 0 || $move >= count($this->board) || $this->board[$move] != 0) {
					throw new WrongMoveException($move);
				}
				$this->numberOfTurns += 1;
				if ($player == $this->player1) {
					# insert 'X', represented as 1, at position $move
					$this->board[$move] = 1;
					$this->currentPlayer = $this->player2;
				} else {
					# insert 'O', represented as -1, at position $move
					$this->board[$move] = -1;
					$this->currentPlayer = $this->player1;
				}
			}

			# check win conditions every time a move is played
			# end the game if a player has won or there is a tie
			if ($this->checkBoardWin($move, $player) || $this->checkBoardTie()) {
				$this->endGame();
			} 
		}

		# player resigns
		public function resign($playerResigned) {
			if (!$this->gameAlive) {
				throw new GameExistenceException(1);
			} else if ($playerResigned != $this->player1 && $playerResigned != $this->player2) {
				throw new PlayerNotInGameException($playerResigned);
			} 
			$this->loser = $playerResigned;
			$this->winner = ($this->player1 != $playerResigned ? $this->player1 : $this->player2); 
			$this->endGame();
		}

		# player raises a tie flag
		# if both players raise a tie flag, the game is tied
		public function raiseTieFlag($playerAskingForTie) {
			if (!$this->gameAlive) {
				throw new GameExistenceException(1);
			} else if ($playerAskingForTie != $this->player1 && $playerAskingForTie != $this->player2) {
				throw new PlayerNotInGameException($playerAskingForTie);
			} 
			if ($playerAskingForTie == $this->player1) {
				$this->player1TieFlag = true;
			} else {
				$this->player2TieFlag = true;
			}
			if ($this->player1TieFlag && $this->player2TieFlag) {
				$this->tie = true;
				$this->endGame();
			}
		}

		# end the game
		private function endGame() {
			$this->gameAlive = false;
			if (!$this->tie) {
				# record game results in leaderboard
				if (array_key_exists($this->winner, $this->leaderboard)) {
					$this->leaderboard[$this->winner]['wins'] += 1;
					$this->leaderboard[$this->winner]['W\L'] = $this->leaderboard[$this->winner]['wins']/max(1, $this->leaderboard[$this->winner]['losses']);
				} else {
					$this->leaderboard[$this->winner]['wins'] = 1;
					$this->leaderboard[$this->winner]['losses'] = 0;
					$this->leaderboard[$this->winner]['W\L'] = 1;
				}

				if (array_key_exists($this->loser, $this->leaderboard)) {
					$this->leaderboard[$this->loser]['losses'] += 1;
					$this->leaderboard[$this->loser]['W\L'] = $this->leaderboard[$this->loser]['wins']/max(1, $this->leaderboard[$this->loser]['losses']);
				} else {
					$this->leaderboard[$this->loser]['wins'] = 0;
					$this->leaderboard[$this->loser]['losses'] = 1;
					$this->leaderboard[$this->loser]['W\L'] = 0;
				}	

				uasort($this->leaderboard, function($a, $b) {
				    return $a['W\L'] < $b['W\L'];
				});			
			} 
		}

		# check the board for a win condition
		# set the board state to win if a win is found
		private function checkBoardWin($move, $lastPlayer) {
			# determine whether a player wins by checking the sum of a 
			# row, column, diagonal AFFECTED by the last move

			$playerWinSum = ($lastPlayer == $this->player1 ? $this->boardLength : -1 * $this->boardLength);

			# check row win condition
			$row = floor($move / $this->boardLength);
			$rowStartIndex = $row * $this->boardLength;
			$rowSum = 0;
			for ($i = $rowStartIndex; $i < $rowStartIndex + $this->boardLength; $i++) {
				$rowSum += $this->board[$i];
			}
			if ($rowSum == $playerWinSum) {
				$this->boardWinPatterns['row'] = $row;
				$this->winner = $lastPlayer;
			}

			# check column win condition
			$column = $move % $this->boardLength;
			$columnStartIndex = $column;
			$columnSum = 0;
			for ($i = $columnStartIndex; $i < $columnStartIndex + $this->boardLength * $this->boardLength; $i += $this->boardLength) {
				$columnSum += $this->board[$i];
			}
			if ($columnSum == $playerWinSum) {
				$this->boardWinPatterns['column'] = $column;
				$this->winner = $lastPlayer;
			}

			# check diagonal win conditions
			# displacement is the difference between the diagonal's successive indicies
			$displacementLR = $this->boardLength + 1;
			$displacementRL = $this->boardLength - 1;
			$diagonalLRSum = 0;
			$diagonalRLSum = 0;
			if ($move % $displacementLR == 0 || $move % $displacementRL == 0) {
				# move is on either diagonal
				for ($i = 0; $i < $this->boardLength; $i++) {
					# check every index of the diagonals
					$diagonalLRSum += $this->board[$displacementLR * $i];
					$diagonalRLSum += $this->board[$displacementRL + $displacementRL * $i];
				}
				if ($diagonalLRSum == $playerWinSum) {
					$this->boardWinPatterns['diagonalLR'] = $displacementLR;
					$this->winner = $lastPlayer;
				} 
				if ($diagonalRLSum == $playerWinSum) {
					$this->boardWinPatterns['diagonalRL'] = $displacementRL;
					$this->winner = $lastPlayer;
				} 
			}

			if ($this->winner != '') {
				# return true if a winner is found
				$this->loser = ($this->player1 == $this->winner ? $this->player2 : $this->player1); 
				return true;
			}
			return false;
 		}

		# check the board for a tie condition
		# set the board state to tied if a tie is found
 		private function checkBoardTie() {
 			if ($this->numberOfTurns == $this->boardLength * $this->boardLength) {
 				$this->tie = true;
 				return true;
 			}
 			return false;
 		}
	}
?>