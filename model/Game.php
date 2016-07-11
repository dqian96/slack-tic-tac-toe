<?php 
	require_once("GameExceptions.php");

	class Game {
		# bool indicating if there's an active game
		private $gameAlive; 

		# players in current game
		private $player1;
		private $player2;
		private $currentPlayer;
		
		# if both players raise a tie flag, then the game ends in a tie (discarded) 
		private $player1TieFlag;
		private $player2TieFlag;

		# winner, loser, tie of prev game
		private $winner;
		private $loser
		private $tie;

		# winningMethod is an array containing the winning rows, columns, diagonals
		private $winningMethod;

		# array of 0,-1,1 modelling the game board
		# player 1 (X) -- 1
		# player 2 (O) -- -1
		# unplayed -- 0
		private $board;
		# width of board (the board is always a square with odd width)
		private $boardLength;

		private $numberOfTurns;

		# 2D array storing past players in sorted order of W/L ratio
		private $leaderboard;
		

		public function __construct() {
 			$leaderboard = array();
 		}

		public function newGame($boardLength, $player1, $player2) {
			if (!($boardLength >= 3 and $boardLength <= 99 and $boardLength % 2 == 1)) {
				throw BoardException($boardLength);
			}

			# set up players
			$this->player1 = $player1;
			$this->player2 = $player2;
			$player1TieFlag = false;
			$player2TieFlag = false;

			$currentPlayer = $player1;
			
			$this->boardLength = $boardLength;

			# set up board, with dimensions, boardLength x boardLength
			$board = array();
			for ($i = 0; $i < $boardLength * $boardLength; $i++) {
				$board[] = $i;
			}

			$winner = '';
			$loser = '';
			$tie = false;

			$winningMethod = array(
				'row' => -1,
				'column' => -1,
				'diagonalLR' => -1,
				'diagonalRL' => -1,
			);

			$gameAlive = true;
			$this->numberOfTurns = 0;
		}

		public function makeMove($player, $move) {
			if ($player != $currentPlayer) {
				if ($player != $player1 and $player != $player2) {
					throw PlayerNotInGameException($player);
				} else {
					throw WrongTurnException($player, $currentPlayer);
				}

			} else {
				$numberOfTurns += 1;
				if ($player == $player1) {
					# insert 'X', represented as 1, at position $move
					$board[$move] = 1;
					$currentPlayer == $player2;
				} else {
					# insert 'O', represented as -1, at position $move
					$board[$move] = -1;
					$currentPlayer == $player1;
				}
			}

			if (checkWin()) {
				endGame();
			} else {
				if (isBoardFull()) {
					tie = true;
					endGame();
				}
			}
		}

		public function resign($playerResigned) {
			$loser = $playerResigned;
			$winner = ($player1 != $playerResigned ? $player1 : $player2); 
			endGame();
		}

		public function tie($playerAskingForTie) {
			if ($playerAskingForTie == $player1) {
				$player1TieFlag = true;
			} else {
				$player2TieFlag = true;
			}
			if ($player1TieFlag and $player2TieFlag) {
				tie = true;
				endGame();
			}
		}


		public function getTie() const {
			return $tie;
		}

		public function getWinner() const {
			return $winner;
		}

		public function getLoser() const {
			return $loser;
		}

		public function getGameAlive() const {
			return $gameAlive;
		}

		public function getBoard() const {
			return $board;
		}

		public function getLeaderboard() const {
			return $leaderboard;
		}

		public function getNumberOfTurns() const {
			return $numberOfTurns;
		}
		
		private function endGame() {
			$gameAlive = false;

			if (!tie) {
				# record game results in leaderboard
				if (in_array($winner, $leaderboard)) {
					$leaderboard[$winner]['wins'] += 1;
					$leaderboard[$winner]['W\L'] += $leaderboard[$winner]['wins']*1.0/max(1, $leaderboard[$winner]['losses']);
				} else {
					$leaderboard[$winner]['wins'] = 1;
					$leaderboard[$winner]['losses'] = 0;
					$leaderboard[$winner]['W\L'] = 1;
				}

				if (in_array($loser, $leaderboard)) {
					$leaderboard[$loser]['losses'] += 1;
					$leaderboard[$loser]['W\L'] += $leaderboard[$loser]['wins']*1.0/max(1, $leaderboard[$loser]['losses']);
				} else {
					$leaderboard[$loser]['wins'] = 0;
					$leaderboard[$loser]['losses'] = 1;
					$leaderboard[$loser]['W\L'] = 0;
				}	

				uasort($leaderboard, function($a, $b) {
				    return $a['W\L'] > $b['W\L'];
				});			
			} 
		}

		private function checkWin($move, $lastPlayer) {
			# determine whether a player wins by checking the sum of a 
			# row, column, and diagonal AFFECTED by the last move

			$playerWinSum = $boardLength;
			if ($lastPlayer != $player1) {
				$playerWinSum = -1 * $boardLength;
			}

			# check row win condition
			$row = $move / $boardLength;
			$rowStartIndex = $row * $boardLength;
			$rowSum = 0;
			for ($i = $rowStartIndex; $i < $rowStartIndex + $boardLength; $i++) {
				$rowSum += $board[$i];
			}
			if ($rowSum == $playerWinSum) {
				$winningMethod['row'] = $row;
				$winner = $lastPlayer;
			}

			# check column win condition
			$column = $move % $boardLength;
			$columnStartIndex = $column;
			$columnSum = 0;
			for ($i = $columnStartIndex; $i < $columnSum + $boardLength * $boardLength; $i += 3) {
				$columnSum += $board[$i];
			}
			if ($columnSum == $playerWinSum) {
				$winningMethod['column'] = $column;
				$winner = $lastPlayer;
			}

			# check diagonal win conditions
			# displacement is the difference between the diagonal's successive indicies
			$displacementLR = $boardLength + 1;
			$displacementRL = $boardLength - 1;
			$diagonalLRSum = 0;
			$diagonalRLSum = 0;
			if ($move % $displacementLR == 0 or $move % $displacementRL == 0) {
				# move is on either diagonal
				for ($i = 0; $i < $boardLength; $i++) {
					# check every index of the diagonals
					$diagonalLRSum += $board[$displacementLR * $i];
					$diagonalRLSum += $board[$displacementRL + $displacementRL * $i]
				}
				if ($diagonalLRSum == $playerWinSum) {
					$winningMethod['diagonalLR'] = $displacementLR;
					$winner = $lastPlayer;
				} 
				if ($diagonalRLSum == $playerWinSum) {
					$winningMethod['diagonalRL'] = $displacementRL;
					$winner = $lastPlayer;
				} 
			}


			if ($winner != '') {
				$loser = ($player1 == $winner ? $player2 : $player1); 
				return true;
			}
			return false;
 		}

 		private function isBoardFull() {
 			if ($numberOfTurns == $boardLength * $boardLength) {
 				return true;
 			}
 			return false;
 		}

	}



?>