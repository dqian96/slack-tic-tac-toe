<?php 
	# class for generating board images
	class GameBoardImage {
		private $boardResource;
		private $configs;

		public function __construct($boardLength, $moves, $boardWinPatterns) {
			$this->configs = include(__DIR__ . '/../resources/game-config.php');
			$squareLength = $this->configs['squareLength'];
			# create a new image resource
			$this->boardResource = imagecreatetruecolor($boardLength * $squareLength, $boardLength * $squareLength);
			
			# define colors to be used
			$white = imagecolorallocate($this->boardResource, 255, 255, 255);
			$black = imagecolorallocate($this->boardResource, 0, 0, 0);
			$green = imagecolorallocate($this->boardResource, 0, 128, 0);

			# fill the image with white
			imagefill($this->boardResource, 0, 0, $white);

			# draw the game grid with black
			$this->drawGrid($squareLength, $black);

			# draw the moves, using green for winning moves
			$this->drawMoves($squareLength, $moves, $boardWinPatterns, $green, $black, $boardLength);
		}

		# create an image from the image resources and return the path
		public function outputImage() {
			//header('Content-Type: image/png');
			$savePath = $_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/public/images/' . $this->configs['boardImageName'];
			$publicPath = __DIR__ . '/../../public/images/' . $this->configs['boardImageName'];
			imagepng($this->boardResource, $savePath);
			return $publicPath;
		}

		private function drawGrid($squareLength, $color) {
			# draw the board grid
			$point1 = array(0, 0);
			$point2 = array(0, imagesy($this->boardResource));
			for ($i = $squareLength; $i < imagesx($this->boardResource); $i += $squareLength) {
				$point1[0] = $i;
				$point2[0] = $i;
				imageline($this->boardResource, $point1[0], $point1[1], $point2[0], $point2[1], $color);
				imageline($this->boardResource, $point1[1], $point1[0], $point2[1], $point2[0], $color);
			}
		}

		# draw the moves played on the board
		private function drawMoves($squareLength, $moves, $boardWinPatterns, $winningColor, $normalColor, $boardLength) {
			# Fill x% of the square width and length with an X or O
			# For example, if it is 0.8, then there'll be a 10% margin of all sides
			$percentLengthFill = 0.8;
			$markerLength = $squareLength * $percentLengthFill;

			for ($position = 0; $position < count($moves); $position++) {
			 	if ($moves[$position] != 0) {
			 		$center = $this->findSquareCenter($position, $squareLength, $boardLength);
			 		$color = ($this->isWinningMove($position, $boardLength, $boardWinPatterns) ? $winningColor : $normalColor);
			 		if ($moves[$position] == -1) {
			 			# draw a circle at coordinate center
			 			imageellipse ($this->boardResource , $center[0] , $center[1] , $markerLength, $markerLength , $color);
			 		} else {
			 			# draw a cross at coordinate center
			 			$this->drawCross($center, $markerLength, $color);
			 		}
			 	}
			 }
		}

		# find the coordinates in the ceneter of a square (move slot)
		private function findSquareCenter($position, $squareLength, $boardLength) {
			# determine row and column of position (index)
			$row = floor($position / $boardLength);
			$column = $position % $boardLength;

			# determine center of square
			$cy = floor($squareLength/2) + $row * $squareLength;
			$cx = floor($squareLength/2) + $column * $squareLength;

			return array($cx, $cy);
		}

		# determine if a move/position is a winning move
		private function isWinningMove($position, $boardLength, $boardWinPatterns) {
			$row = floor($position / $boardLength);
			$column = $position % $boardLength;
			# determine if a position is a member of the set of winning moves
			# i.e. check if the position is on the winning row, column, or diagonals
			if 
				(
				$row == $boardWinPatterns['row'] || 
				$column == $boardWinPatterns['column'] ||
				# every position that is a multiple of diagonalLR = board length + 1
				# is on the L-R diagonal
				($boardWinPatterns['diagonalLR'] != -1 && $position % $boardWinPatterns['diagonalLR'] == 0) || 
				# every position that is a multiple of diagonalRL = board length - 1
				# EXCEPT positions 0 and boardlength^2 - 1 is on the R-L diagonal
				($boardWinPatterns['diagonalRL'] != -1 && $position % $boardWinPatterns['diagonalRL'] == 0 && $position != 0 && $position != $boardLength * $boardLength  - 1) 
				) 
			{
				return true;
			}

			return false; 
		}

		# draw an X given a center position
		private function drawCross($center, $markerLength, $color) {
			# draw the L-R line
			$point1 = array( $center[0] - floor($markerLength/2), $center[1] - floor($markerLength/2));
			$point2 = array( $center[0] + floor($markerLength/2), $center[1] + floor($markerLength/2));		
			imageline($this->boardResource, $point1[0], $point1[1], $point2[0], $point2[1], $color);

			# draw the R-L line
			$point1 = array( $center[0] - floor($markerLength/2), $center[1] + floor($markerLength/2));
			$point2 = array( $center[0] + floor($markerLength/2), $center[1] - floor($markerLength/2));		
			imageline($this->boardResource, $point1[0], $point1[1], $point2[0], $point2[1], $color);
		}
	}
?>