<?php 
	# class for generating board images
	class GameBoardImage {
		private $boardResource;
		private $configs;

		public function __construct($boardLength, $moves, $winData) {
			$this->configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/game-config.php');
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
			$this->drawMoves($squareLength, $moves, $winData, $green, $black, $boardLength);
		}

		# create an image from the image resources and return the path
		public function outputImage() {
			//header('Content-Type: image/png');
			$savePath = $_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/public/images/' . $this->configs['boardImageName'];
			$publicPath = $_SERVER['SERVER_NAME'] . '/public/images/' . $this->configs['boardImageName'];
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
		private function drawMoves($squareLength, $moves, $winData, $winningColor, $normalColor, $boardLength) {
			# Fill x% of the square width and length with an X or O
			# For example, if it is 0.8, then there'll be a 10% margin of all sides
			$percentLengthFill = 0.8;
			$markerLength = $squareLength * $percentLengthFill;

			for ($position = 0; $position < count($moves); $position++) {
			 	if ($moves[$position] != 0) {
			 		$center = $this->findSquareCenter($position, $squareLength, $boardLength);
			 		$color = ($this->isWinningMove($position, $boardLength, $winData) ? $winningColor : $normalColor);
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
		private function isWinningMove($position, $boardLength, $winData) {
			$row = floor($position / $boardLength);
			$column = $position % $boardLength;
			if 
				(
				$row == $winData['row'] or 
				$column == $winData['column'] or
				($winData['diagonalLR'] != -1 and $position % $winData['diagonalLR'] == 0) or 
				($winData['diagonalRL'] != -1 and $position - % $winData['diagonalRL'] == 0 and $position != 0) 
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